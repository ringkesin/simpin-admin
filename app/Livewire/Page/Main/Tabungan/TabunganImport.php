<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use Illuminate\Database\QueryException;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TabunganTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory; // ✅ Impor ini!
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TabunganModels;
use App\Models\Master\AnggotaModels;
use Livewire\WithFileUploads;

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

    public function mount() {
        $this->titlePage = 'Import Tabungan Anggota';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.import'), 'label' => 'Import']
        ];
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
                !empty($row[2])
                ) {
                $this->data[] = [
                    'nomor_anggota' => trim($row[0]),
                    'bulan' => $row[1],
                    'tahun' => $row[2],
                    'simpanan_pokok' => floatval($row[3]),
                    'simpanan_wajib' => floatval($row[4]),
                    'tabungan_sukarela' => floatval($row[5]),
                    'tabungan_indir' => floatval($row[6]),
                    'kompensasi_masa_kerja' => floatval($row[7]),
                ];
            }
        }
    }

    public function uploadFiles()
    {
        // Simpan data ke database (contoh)
        try {
            foreach ($this->data as $dataLoop) {
                $dataFind = AnggotaModels::where('nomor_anggota', $dataLoop['nomor_anggota'])->first();
                if(isset($dataFind['p_anggota_id'])) {
                    $checkDuplicatePeriod = TabunganModels::where([
                        ['bulan', '=', $dataLoop['bulan']],
                        ['tahun', '=', $dataLoop['tahun']],
                        ['p_anggota_id', '=', $dataFind['p_anggota_id']]
                    ])->count();

                    if($checkDuplicatePeriod == 0) {
                        TabunganModels::create([
                            'p_anggota_id' => $dataFind['p_anggota_id'],
                            'bulan' => $dataLoop['bulan'],
                            'tahun' => $dataLoop['tahun'],
                            'simpanan_pokok' => $dataLoop['simpanan_pokok'],
                            'simpanan_wajib' => $dataLoop['simpanan_wajib'],
                            'tabungan_sukarela' => $dataLoop['tabungan_sukarela'],
                            'tabungan_indir' => $dataLoop['tabungan_indir'],
                            'kompensasi_masa_kerja' => $dataLoop['kompensasi_masa_kerja'],
                        ]);
                    }
                }
            }
            // dd(route('main.tabungan.list'));
            $redirect = route('main.tabungan.list');
            return $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => $redirect
            ]);
        } catch (QueryException $e) {
            $textError = '';
            if($e->errorInfo[1] == 1062) {
                $textError = 'Data gagal di update karena duplikat data, coba kembali.';
            } else {
                $textError = 'Data gagal di update, coba kembali.';
            }
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $textError,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.page.main.tabungan.tabungan-import')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
