<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use Illuminate\Database\QueryException;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory; // ✅ Impor ini!
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TagihanModels;
use App\Models\Master\AnggotaModels;
use Livewire\WithFileUploads;

class TagihanImport extends Component
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
        $this->titlePage = 'Import Tagihan Anggota';
        $this->menuCode = 'tagihan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tagihan'],
            ['link' => route('main.tagihan.list'), 'label' => 'List'],
            ['link' => route('main.tagihan.import'), 'label' => 'Import']
        ];
    }

    public function downloadTemplate()
    {
        return Excel::download(new TagihanTemplateExport, 'template_tagihan.xlsx');
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
                    'uraian' => $row[3],
                    'jumlah' => floatval($row[4]),
                    'remarks' => $row[5]
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
                    TagihanModels::create([
                        'p_anggota_id' => $dataFind['p_anggota_id'],
                        'bulan' => $dataLoop['bulan'],
                        'tahun' => $dataLoop['tahun'],
                        'uraian' => $dataLoop['uraian'],
                        'jumlah' => $dataLoop['jumlah'],
                        'remarks' => $dataLoop['remarks']
                    ]);
                }
            }
            // dd(route('main.tagihan.list'));
            $redirect = route('main.tagihan.list');
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
        return view('livewire.page.main.tagihan.tagihan-import')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
