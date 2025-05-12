<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory; // ✅ Impor ini!
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TagihanModels;
use App\Models\Master\AnggotaModels;
use App\Models\Master\StatusPembayaranModels;
use App\Models\Master\MetodePembayaranModels;
use App\Models\Main\PinjamanModels;
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
                !empty($row[2]) &&
                !empty($row[3])
                ) {
                $this->data[] = [
                    'nomor_anggota' => trim($row[0]),
                    'nomor_pinjaman' => trim($row[1]),
                    'bulan' => $row[2],
                    'tahun' => $row[3],
                    'uraian' => $row[4],
                    'jumlah_tagihan' => floatval($row[5]),
                    'remarks' => $row[6],
                    'tgl_jatuh_tempo' => trim($row[7]),
                    'status_pembayaran' => trim($row[8]),
                    'tgl_dibayar' => trim($row[9]),
                    'jumlah_dibayarkan' => floatval($row[10]),
                    'metode_pembayaran' => $row[11]
                ];
            }
        }
    }

    public function uploadFiles()
    {
        // Simpan data ke database (contoh)
        try {
            DB::beginTransaction();
            foreach ($this->data as $dataLoop) {
                $dataFind = AnggotaModels::where('nomor_anggota', $dataLoop['nomor_anggota'])->first();
                $dataPinjaman = NULL;
                $statusFind = NULL;
                $metodeFind = NULL;

                if(isset($dataLoop['nomor_pinjaman'])) {
                    $dataPinjaman = PinjamanModels::where('nomor_pinjaman', $dataLoop['nomor_pinjaman'])->first();
                }

                if(isset($dataLoop['status_pembayaran'])) {
                    $statusFind = StatusPembayaranModels::where('status_code', $dataLoop['status_pembayaran'])->first();
                }

                if(isset($dataLoop['metode_pembayaran'])) {
                    $metodeFind = MetodePembayaranModels::where('metode_code', $dataLoop['metode_pembayaran'])->first();
                }

                $payload = [
                        'p_anggota_id' => $dataFind['p_anggota_id'],
                        't_pinjaman_id' => isset($dataPinjaman) ? $dataPinjaman['t_pinjaman_id'] : NULL,
                        'bulan' => $dataLoop['bulan'],
                        'tahun' => $dataLoop['tahun'],
                        'uraian' => $dataLoop['uraian'],
                        'jumlah_tagihan' => $dataLoop['jumlah_tagihan'],
                        'remarks' => $dataLoop['remarks'],
                        'tgl_jatuh_tempo' => $dataLoop['tgl_jatuh_tempo'],
                        'p_status_pembayaran_id' => isset($statusFind) ? $statusFind['p_status_pembayaran_id'] : NULL,
                        'paid_at' => $dataLoop['tgl_dibayar'],
                        'jumlah_pembayaran' => $dataLoop['jumlah_dibayarkan'],
                        'p_metode_pembayaran_id' => isset($metodeFind) ? $metodeFind['p_metode_pembayaran_id'] : NULL
                ];

                if(isset($dataFind['p_anggota_id'])) {
                    TagihanModels::create($payload);
                }
            }
            DB::commit();
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
