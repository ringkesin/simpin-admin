<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagihanTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory; // ✅ Impor ini!
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Exports\TagihanExport;

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
    public $bulan;
    public $tahun;

    public function mount() {
        $this->titlePage = 'Import & Export Tagihan Anggota';
        $this->menuCode = 'tagihan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tagihan'],
            ['link' => route('main.tagihan.list'), 'label' => 'List'],
            ['link' => route('main.tagihan.import'), 'label' => 'Import & Export']
        ];
    }

    public function downloadTemplate()
    {
        return Excel::download(new TagihanTemplateExport, 'template_tagihan.xlsx');
    }

    public function exportTagihan()
    {
        $this->validate([
            'bulan' => 'required',
            'tahun' => 'required|digits:4',
        ]);

        // return redirect()->route('export.tagihan.download');
        return Excel::download(new TagihanExport($this->bulan, $this->tahun), 'tagihan_'. $this->bulan .'-' . $this->tahun . '.xlsx');
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
                !empty($row[1]) &&
                !empty($row[3]) &&
                !empty($row[4])
                ) {
                $this->data[] = [
                    't_tagihan_id' => $row[0],
                    'nomor_anggota' => trim($row[1]),
                    'nomor_pinjaman' => trim($row[2]),
                    'bulan' => $row[3],
                    'tahun' => $row[4],
                    'uraian' => $row[5],
                    'jumlah_tagihan' => floatval($row[6]),
                    'remarks' => $row[7],
                    'tgl_jatuh_tempo' => trim($row[8]),
                    'status_pembayaran' => trim($row[9]),
                    'tgl_dibayar' => trim($row[10]),
                    'jumlah_dibayarkan' => floatval($row[11]),
                    'metode_pembayaran' => $row[12]
                ];
            }
        }
    }

    public function uploadFilesOld()
    {
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

            foreach ($this->data as $dataLoop) {
                $dataFind = AnggotaModels::where('nomor_anggota', $dataLoop['nomor_anggota'])->firstOrFail();
                $dataPinjaman = PinjamanModels::where('nomor_pinjaman', $dataLoop['nomor_pinjaman'] ?? null)->first();
                $statusFind = StatusPembayaranModels::where('status_code', $dataLoop['status_pembayaran'] ?? null)->first();
                $metodeFind = MetodePembayaranModels::where('metode_code', $dataLoop['metode_pembayaran'] ?? null)->first();

                $payload = [
                    'p_anggota_id' => $dataFind->p_anggota_id,
                    't_pinjaman_id' => $dataPinjaman?->t_pinjaman_id,
                    'bulan' => $dataLoop['bulan'],
                    'tahun' => $dataLoop['tahun'],
                    'uraian' => $dataLoop['uraian'],
                    'jumlah_tagihan' => $dataLoop['jumlah_tagihan'],
                    'remarks' => $dataLoop['remarks'],
                    'tgl_jatuh_tempo' => $dataLoop['tgl_jatuh_tempo'] ? $dataLoop['tgl_jatuh_tempo'] : NULL,
                    'p_status_pembayaran_id' => $statusFind?->p_status_pembayaran_id,
                    'paid_at' => $dataLoop['tgl_dibayar'] ? $dataLoop['tgl_dibayar'] : NULL,
                    'jumlah_pembayaran' => $dataLoop['jumlah_dibayarkan'] ? $dataLoop['jumlah_dibayarkan'] : NULL,
                    'p_metode_pembayaran_id' => $metodeFind?->p_metode_pembayaran_id,
                ];

                if(!empty($dataLoop['t_tagihan_id'])) {
                    // dd($payload);
                    TagihanModels::where('t_tagihan_id', $dataLoop['t_tagihan_id'])->update(
                        $payload
                    );
                } else {
                    TagihanModels::create($payload);
                }

            }

            DB::commit();

            return $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => route('main.tagihan.list'),
            ]);

        } catch (QueryException $e) {
            DB::rollBack();
            $textError = $e->errorInfo[1] == 1062
                ? 'Data gagal di update karena duplikat data, coba kembali.'
                : 'Data gagal di update, coba kembali.';

            return $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $textError,
            ]);
        }
    }

    public function uploadFiles()
    {
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

            // Ambil daftar referensi yang dibutuhkan
            $nomorAnggotaList = collect($this->data)->pluck('nomor_anggota')->unique();
            $nomorPinjamanList = collect($this->data)->pluck('nomor_pinjaman')->filter()->unique();
            $statusCodeList = collect($this->data)->pluck('status_pembayaran')->filter()->unique();
            $metodeCodeList = collect($this->data)->pluck('metode_pembayaran')->filter()->unique();

            // Ambil semua data referensi sekaligus
            $anggotaList = AnggotaModels::whereIn('nomor_anggota', $nomorAnggotaList)->get()->keyBy('nomor_anggota');
            $pinjamanList = PinjamanModels::whereIn('nomor_pinjaman', $nomorPinjamanList)->get()->keyBy('nomor_pinjaman');
            $statusList = StatusPembayaranModels::whereIn('status_code', $statusCodeList)->get()->keyBy('status_code');
            $metodeList = MetodePembayaranModels::whereIn('metode_code', $metodeCodeList)->get()->keyBy('metode_code');

            $payloads = [];
            $now = now();

            foreach ($this->data as $dataLoop) {
                $anggota = $anggotaList[$dataLoop['nomor_anggota']] ?? null;
                if (!$anggota) {
                    continue; // skip jika tidak ada anggota
                }

                $payload = [
                    't_tagihan_id' => $dataLoop['t_tagihan_id'] ?? substr(str_replace('-', '', Str::uuid()->toString()), 0, 26),
                    'p_anggota_id' => $anggota->p_anggota_id,
                    't_pinjaman_id' => $pinjamanList[$dataLoop['nomor_pinjaman']]?->t_pinjaman_id ?? null,
                    'bulan' => $dataLoop['bulan'],
                    'tahun' => $dataLoop['tahun'],
                    'uraian' => $dataLoop['uraian'],
                    'jumlah_tagihan' => $dataLoop['jumlah_tagihan'],
                    'remarks' => $dataLoop['remarks'],
                    'tgl_jatuh_tempo' => !empty($dataLoop['tgl_jatuh_tempo']) ? $dataLoop['tgl_jatuh_tempo'] : null,
                    'p_status_pembayaran_id' => $statusList[$dataLoop['status_pembayaran']]?->p_status_pembayaran_id ?? 2,
                    'paid_at' => !empty($dataLoop['tgl_dibayar']) ? $dataLoop['tgl_dibayar'] : null,
                    'jumlah_pembayaran' => $dataLoop['jumlah_dibayarkan'] ?? null,
                    'p_metode_pembayaran_id' => $metodeList[$dataLoop['metode_pembayaran']]?->p_metode_pembayaran_id ?? null,
                ];

                $payloads[] = $payload;
            }

            // dd($payloads);

            if (!empty($payloads)) {
                TagihanModels::upsert($payloads, ['t_tagihan_id'], [
                    'p_anggota_id', 't_pinjaman_id', 'bulan', 'tahun', 'uraian', 'jumlah_tagihan',
                    'remarks', 'tgl_jatuh_tempo', 'p_status_pembayaran_id', 'paid_at', 'jumlah_pembayaran',
                    'p_metode_pembayaran_id', 'updated_at'
                ]);
            }

            DB::commit();

            return $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => route('main.tagihan.list'),
            ]);

        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
            $textError = $e->errorInfo[1] == 1062
                ? 'Data gagal di update karena duplikat data, coba kembali.'
                : 'Data gagal di update, coba kembali.';

            return $this->sweetalert([
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
