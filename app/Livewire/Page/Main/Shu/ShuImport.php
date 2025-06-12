<?php

namespace App\Livewire\Page\Main\Shu;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShuTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory; // ✅ Impor ini!
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\ShuExport;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\ShuModels;
use App\Models\Master\AnggotaModels;
use Livewire\WithFileUploads;

class ShuImport extends Component
{
    use MyAlert;
    use MyHelpers;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $files;
    public $data;
    public $tahun;

    public function mount() {
        $this->titlePage = 'Import & Export  SHU Anggota';
        $this->menuCode = 'shu';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'SHU'],
            ['link' => route('main.shu.list'), 'label' => 'List'],
            ['link' => route('main.shu.import'), 'label' => 'Import & Export']
        ];
    }

    public function downloadTemplate()
    {
        return Excel::download(new ShuTemplateExport, 'template_shu.xlsx');
    }

    public function exportSHU()
    {
        $this->validate([
            'tahun' => 'required|digits:4',
        ]);

        // return redirect()->route('export.tagihan.download');
        return Excel::download(new ShuExport($this->tahun), 'shu_'. $this->tahun . '.xlsx');
    }

    public function updatedFiles()
    {
        $this->validate([
            'files' => 'required|file|max:10240', // Maksimum 10MB per file
        ]);
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
            return 400;
        }

        // Hapus header dan simpan data ke array
        array_shift($rows);

        foreach ($rows as $row) {
            if (
                !empty($row[0]) &&
                !empty($row[1])
                ) {
                $this->data[] = [
                    'nomor_anggota' => trim($row[0]),
                    'tahun' => $row[1],
                    'shu_diterima' => floatval($row[2]),
                    // 'shu_dibagi' => floatval($row[3]),
                    // 'shu_ditabung' => floatval($row[4]),
                    // 'shu_tahun_lalu' => floatval($row[4])
                ];
            }
        }
    }

    public function uploadFilesOld()
    {
        // Simpan data ke database (contoh)
        try {
            if($this->convertToJson() == 400) {
                return $this->sweetalert([
                    'icon' => 'error',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'File Excel tidak memiliki cukup data.',
                ]);
            }

            // Ambil semua nomor_anggota yang dibutuhkan
            $nomorAnggotaList = collect($this->data)->pluck('nomor_anggota')->unique()->toArray();

            // Ambil semua anggota sekaligus, lalu indeks berdasarkan nomor_anggota
            $anggotaList = AnggotaModels::whereIn('nomor_anggota', $nomorAnggotaList)->get()->keyBy('nomor_anggota');

            // Ambil semua (p_anggota_id, tahun) yang sudah ada di tabel SHU
            $existingRecords = ShuModels::whereIn('p_anggota_id', $anggotaList->pluck('p_anggota_id'))
                ->whereIn('tahun', collect($this->data)->pluck('tahun'))
                ->get()
                ->map(fn($item) => $item->p_anggota_id . '-' . $item->tahun)
                ->toArray();

            $dataInsert = [];
            $dataUpdate = [];
            $insertAvailable = false;
            $updateAvailable = false;

            DB::beginTransaction();

            foreach ($this->data as $dataLoop) {
                // $dataFind = AnggotaModels::where('nomor_anggota', $dataLoop['nomor_anggota'])->first();
                $anggota = $anggotaList[$dataLoop['nomor_anggota']] ?? null;

                if($anggota) {
                    $checkDuplicatePeriod = ShuModels::where([
                        ['tahun', '=', $dataLoop['tahun']],
                        ['p_anggota_id', '=', $anggota->p_anggota_id]
                    ])->count();

                    if($checkDuplicatePeriod == 0) {
                        $insertAvailable = true;
                        $dataInsert[] = [
                            'p_anggota_id' => $anggota->p_anggota_id,
                            'tahun' => $dataLoop['tahun'],
                            'shu_diterima' => $dataLoop['shu_diterima'],
                            'shu_dibagi' => 0,
                            'shu_ditabung' => 0,
                            'shu_tahun_lalu' => 0,
                            'created_at' => now(), // wajib kalau pakai timestamps
                            'updated_at' => now(),
                        ];
                        // ShuModels::create([
                        //     'p_anggota_id' => $dataFind['p_anggota_id'],
                        //     'tahun' => $dataLoop['tahun'],
                        //     'shu_diterima' => $dataLoop['shu_diterima'],
                        //     'shu_dibagi' => 0,
                        //     'shu_ditabung' => 0,
                        //     'shu_tahun_lalu' => 0
                        // ]);
                    } else {
                        $updateAvailable = true;
                        $dataUpdate[] = [
                            'p_anggota_id' => $anggota->p_anggota_id,
                            'tahun' => $dataLoop['tahun'],
                            'shu_diterima' => $dataLoop['shu_diterima'],
                            'updated_at' => now(),
                        ];
                        // ShuModels::where('p_anggota_id', $dataFind['p_anggota_id'])
                        //         ->where('tahun', $dataLoop['tahun'])
                        //         ->update(['shu_diterima' => $dataLoop['shu_diterima']]);
                    }
                }
            }

            if($insertAvailable) {
                ShuModels::insert($dataInsert);
            }

            if($updateAvailable) {
                ShuModels::upsert($dataUpdate, ['p_anggota_id', 'tahun'], ['shu_diterima', 'updated_at']);
            }

            $redirect = route('main.shu.list');

            DB::commit();

            return $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => $redirect
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
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

    public function uploadFiles() {
        // Simpan data ke database (contoh)
        DB::beginTransaction();

        try {
            if($this->convertToJson() == 400) {
                return $this->sweetalert([
                    'icon' => 'error',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'File Excel tidak memiliki cukup data.',
                ]);
            }

            // Ambil semua nomor_anggota yang dibutuhkan
            $nomorAnggotaList = collect($this->data)->pluck('nomor_anggota')->unique()->toArray();

            // Ambil semua anggota sekaligus, lalu indeks berdasarkan nomor_anggota
            $anggotaList = AnggotaModels::whereIn('nomor_anggota', $nomorAnggotaList)
                ->get()
                ->keyBy('nomor_anggota');

            // Ambil semua kombinasi (p_anggota_id, tahun) yang sudah ada di tabel SHU
            $existingRecords = ShuModels::whereIn('p_anggota_id', $anggotaList->pluck('p_anggota_id'))
                ->whereIn('tahun', collect($this->data)->pluck('tahun')->unique())
                ->get()
                ->map(fn($item) => $item->p_anggota_id . '-' . $item->tahun)
                ->toArray();

            $dataUpsert = [];

            foreach ($this->data as $dataLoop) {
                $anggota = $anggotaList[$dataLoop['nomor_anggota']] ?? null;

                if ($anggota) {
                    $key = $anggota->p_anggota_id . '-' . $dataLoop['tahun'];

                    $data = [
                        'p_anggota_id' => $anggota->p_anggota_id,
                        'tahun' => $dataLoop['tahun'],
                        'shu_diterima' => $dataLoop['shu_diterima'],
                        'shu_dibagi' => 0,
                        'shu_ditabung' => 0,
                        'shu_tahun_lalu' => 0,
                        'updated_at' => now()
                    ];

                    if (!in_array($key, $existingRecords)) {
                        // Insert baru
                        $data['shu_dibagi'] = 0;
                        $data['shu_ditabung'] = 0;
                        $data['shu_tahun_lalu'] = 0;
                        $data['created_at'] = now();
                    }

                    $dataUpsert[] = $data;
                }
            }

            if (!empty($dataUpsert)) {
                ShuModels::upsert($dataUpsert, ['p_anggota_id', 'tahun'], [
                    'shu_diterima',
                    'shu_dibagi',
                    'shu_ditabung',
                    'shu_tahun_lalu',
                    'updated_at'
                ]);
            }

            DB::commit();

            $redirect = route('main.shu.list');

            return $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => $redirect
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
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
        return view('livewire.page.main.shu.shu-import')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
