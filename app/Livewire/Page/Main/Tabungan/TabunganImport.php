<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TabunganTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TabunganJurnalModels;
use App\Models\Master\AnggotaModels;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Master\JenisTabunganModels;

class TabunganImport extends Component
{
    use MyAlert;
    use MyHelpers;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $files;
    public $data;
    public $jenisTabungan;

    public function mount() {
        $this->titlePage = 'Import Tabungan Anggota';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.import'), 'label' => 'Import']
        ];
        $this->jenisTabungan = JenisTabunganModels::orderBy('p_jenis_tabungan_id')->get();
    }

    public function downloadTemplate()
    {
        return Excel::download(new TabunganTemplateExport, 'template_tabungan.xlsx');
    }

    public function updatedFiles()
    {
        $this->validate([
            'files' => 'required|file|max:10240', // Maksimum 10MB per file
        ]);

        $this->convertToJson();
    }

    public function convertToJson()
    {
        $path = $this->files->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Pastikan data memiliki header yang benar
        if (count($rows) < 2) {
            session()->flash('error', 'File Excel tidak memiliki cukup data.');
            return;
        }

        // Hapus header dan simpan data ke array
        array_shift($rows);

        foreach ($rows as $row) {
            if (
                !empty($row[0]) &&
                !empty($row[1]) &&
                !empty($row[2]) &&
                !empty($row[4])
                ) {
                $this->data[] = [
                    'nomor_anggota' => trim($row[0]),
                    'tgl_transaksi' => trim($row[1]),
                    'p_jenis_tabungan_id' => trim($row[2]),
                    'remarks' => trim($row[3]),
                    'nilai' => floatval(trim($row[4])),
                ];
            }
        }
    }

    public function uploadFiles()
    {
        try {
            $collection = collect($this->data);
            $rules = [
                'nomor_anggota' => 'required|numeric',
                'tgl_transaksi' => 'required|date_format:Y-m-d',
                'p_jenis_tabungan_id' => 'required|numeric',
                'remarks' => 'nullable|string',
                'nilai' => 'required|numeric',
            ];
            $errors = [];
            foreach ($collection as $index => $item) {
                $validator = Validator::make($item, $rules);
                if ($validator->fails()) {
                    $errors[$index] = $validator->errors()->all();
                }
            }

            if (!empty($errors)) {
                $errmsg = "<ul>";
                foreach ($errors as $row => $messages) {
                    foreach ($messages as $message) {
                        $no = $row + 1;
                        $errmsg .= "<li>Row #{$no}: {$message}</li>";
                    }
                }
                $errmsg .= "</ul>";
                return $this->sweetalert([
                    'icon' => 'error',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'html' => $errmsg,
                ]);
            } 

            $collection = collect($this->data);
            $recalculateList = $collection
                ->groupBy('nomor_anggota')
                ->mapWithKeys(function ($items, $nomorAnggota) {
                    $earliest = $items->sortBy('tgl_transaksi')->first();
                    $year = date('Y', strtotime($earliest['tgl_transaksi']));
                    return [$nomorAnggota => $year];
                })
                ->sortBy(fn($year) => $year)
                ->toArray();
            
            DB::beginTransaction();

            $distinctNomorAnggota = collect($this->data)->pluck('nomor_anggota')->unique()->values();
            $distinctList = AnggotaModels::whereIn('nomor_anggota', $distinctNomorAnggota)->get();
            if(count($distinctList) > 0){
                $anggota = [];
                foreach($distinctList as $d){
                    $anggota[$d['nomor_anggota']] = $d->toArray() + [
                        'recalculate_from' => $recalculateList[$d['nomor_anggota']]
                    ];
                }
                
                $batchInsert = [];
                foreach ($this->data as $x) {
                    $batchInsert[] = [
                        't_tabungan_jurnal_id' => strtolower(Str::ulid()),
                        'p_anggota_id' => $anggota[$x['nomor_anggota']]['p_anggota_id'],
                        'p_jenis_tabungan_id' => $x['p_jenis_tabungan_id'],
                        'tgl_transaksi' => $x['tgl_transaksi'].' '.date('H:i:s'),
                        'nilai' => $x['nilai'],
                        'nilai_sd' => 0,
                        'catatan' => empty($x['remarks']) ? null : $x['remarks'],
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ];
                }
                TabunganJurnalModels::insert($batchInsert);
                foreach($anggota as $a){
                    DB::select('SELECT _tabungan_recalculate(:p_anggota_id, :tahun)', [
                        'p_anggota_id' => $a['p_anggota_id'],
                        'tahun' => $a['recalculate_from'],
                    ]);
                }
                DB::commit();

                $redirect = route('main.tabungan.list');
                return $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
            }
            else {
                return $this->sweetalert([
                    'icon' => 'error',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data tidak ada yang terupload',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.page.main.tabungan.tabungan-import')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode,
        ]);
    }
}
