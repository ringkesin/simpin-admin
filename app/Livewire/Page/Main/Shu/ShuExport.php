<?php

namespace App\Livewire\Page\Main\Shu;

use Livewire\Component;
use Illuminate\Database\QueryException;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShuTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory; // ✅ Impor ini!
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\ShuModels;
use App\Models\Master\AnggotaModels;
use Livewire\WithFileUploads;

class ShuExport extends Component
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
        $this->titlePage = 'Export SHU Anggota';
        $this->menuCode = 'shu';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'SHU'],
            ['link' => route('main.shu.list'), 'label' => 'List'],
            ['link' => route('main.shu.export'), 'label' => 'Export']
        ];
    }

    public function downloadTemplate()
    {
        return Excel::download(new ShuTemplateExport, 'template_shu.xlsx');
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
                !empty($row[1])
                ) {
                $this->data[] = [
                    'nomor_anggota' => trim($row[0]),
                    'tahun' => $row[1],
                    'shu_diterima' => floatval($row[2]),
                    'shu_dibagi' => floatval($row[3]),
                    'shu_ditabung' => floatval($row[4])
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
                    $checkDuplicatePeriod = ShuModels::where([
                        ['tahun', '=', $dataLoop['tahun']],
                        ['p_anggota_id', '=', $dataFind['p_anggota_id']]
                    ])->count();

                    if($checkDuplicatePeriod == 0) {
                        ShuModels::create([
                            'p_anggota_id' => $dataFind['p_anggota_id'],
                            'tahun' => $dataLoop['tahun'],
                            'shu_diterima' => $dataLoop['shu_diterima'],
                            'shu_dibagi' => $dataLoop['shu_dibagi'],
                            'shu_ditabung' => $dataLoop['shu_ditabung']
                        ]);
                    }
                }
            }
            $redirect = route('main.shu.list');
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
        return view('livewire.page.main.shu.shu-export')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
