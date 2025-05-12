<?php

namespace App\Livewire\Page\Main\Pinjaman;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;

use App\Models\Main\PinjamanModels;
use App\Models\Master\JenisPinjamanModels;
use App\Models\Master\PinjamanKeperluanModels;
use App\Models\Master\StatusPengajuanModels;
use App\Models\Master\AnggotaModels;

class PinjamanCreate extends Component
{
    use MyAlert;
    use MyHelpers;
    use WithFileUploads;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;
    public $loadDataAnggota = [];
    public $loadDataJenisPinjaman = [];
    public $loadDataKeperluanPinjaman = [];

    #component input
    public $p_anggota_id;
    public $p_jenis_pinjaman_id;
    public $ra_jumlah_pinjaman;
    public $tenor;
    public $biaya_admin = 1.5;
    public $jaminan;
    public $jaminan_keterangan;
    public $jaminan_perkiraan_nilai;
    public $jenis_barang;
    public $merk_type;
    public $no_rekening;
    public $bank;
    public $p_pinjaman_keperluan_ids = [];
    public $doc_slip_gaji;
    public $ri_jumlah_pinjaman = 0;
    public $p_status_pengajuan_id = 2;
    public $remarks;
    public $tgl_pencairan;
    public $tgl_pelunasan;

    public function mount() {
        $this->titlePage = 'Tambah Pinjaman';
        $this->menuCode = 'pinjaman';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Pinjaman'],
            ['link' => route('main.pinjaman.list'), 'label' => 'List'],
            ['link' => route('main.pinjaman.create'), 'label' => 'Create']
        ];
        $this->getDataAnggota();
        $this->getDataJenisPinjaman();
        $this->getDataKeperluanPinjaman();
        $this->addRow();
    }

    public function getDataAnggota() {
        $data = AnggotaModels::all();
        $this->loadDataAnggota = $data;
    }

    public function getDataJenisPinjaman() {
        $data = JenisPinjamanModels::all();
        $this->loadDataJenisPinjaman = $data;
    }

    public function getDataKeperluanPinjaman() {
        $data = PinjamanKeperluanModels::all();
        $this->loadDataKeperluanPinjaman = $data;
    }

    public function listStatusPinjaman() {
        $data = StatusPengajuanModels::all();
        return $data;
    }

    public function addRow()
    {
        $this->p_pinjaman_keperluan_ids[] = [];
    }

    public function removeRow($index)
    {
        unset($this->p_pinjaman_keperluan_ids[$index]);
        $this->p_pinjaman_keperluan_ids = array_values($this->p_pinjaman_keperluan_ids);
    }

    public function totalDisetujui()
    {
        $pinjaman = $this->ri_jumlah_pinjaman ?? 0;
        $admin = $this->biaya_admin ?? 0;

        return $pinjaman + ($pinjaman * ($admin / 100));
    }

    public function saveInsert() {
        $validated = $this->validate([
            'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
            'p_jenis_pinjaman_id' => 'required|integer|exists:p_jenis_pinjaman,p_jenis_pinjaman_id',
            'p_pinjaman_keperluan_ids' => 'required_unless:p_jenis_pinjaman_id,3|array|nullable',
            'p_pinjaman_keperluan_ids.*' => 'exists:p_pinjaman_keperluan,p_pinjaman_keperluan_id|distinct',
            'jenis_barang' => 'required_if:p_jenis_pinjaman_id,3|nullable|string|max:255',
            'merk_type' => 'required_if:p_jenis_pinjaman_id,3|nullable|string|max:255',
            'tenor' => 'required|integer|min:1',
            'ra_jumlah_pinjaman' => 'required|numeric',
            'jaminan' => 'required|string|max:255',
            'jaminan_keterangan' => 'nullable|string|max:255',
            'jaminan_perkiraan_nilai' => 'required|numeric',
            'no_rekening' => 'required|numeric',
            'bank' => 'required|string|max:255',
            'biaya_admin' => 'required|numeric',
            // 'doc_ktp' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            // 'doc_ktp_suami_istri' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            // 'doc_kk' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            // 'doc_kartu_anggota' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'doc_slip_gaji' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ], [
            'p_anggota_id.required' => 'Anggota harus diisi',
            'p_jenis_pinjaman_id.required' => 'Jenis Pinjaman harus diisi',
            'p_pinjaman_keperluan_ids.required' => 'Keperluan Pinjaman harus diisi',
            'jenis_barang.required' => 'Jenis Barang harus diisi',
            'merk_type.required' => 'Merk / Tipe Barang harus diisi',
            'tenor.required' => 'Tenor harus diisi',
            'biaya_admin.required' => 'Biaya Admin harus diisi',
            'ra_jumlah_pinjaman.required' => 'Jumlah Pengajuan Pinjaman harus diisi',
            'jaminan.required' => 'Jaminan harus diisi',
            'jaminan_perkiraan_nilai.required' => 'Perkiraan Nilai Jaminan harus diisi',
            'no_rekening.required' => 'Nomor Rekening harus diisi',
            'bank.required' => 'Bank harus diisi',
            // 'doc_ktp' => 'File KTP Pemohon harus diupload',
            // 'doc_ktp_suami_istri' => 'File KTP Suami / Istri harus diupload',
            // 'doc_kk' => 'File KK harus diupload',
            // 'doc_kartu_anggota' => 'File Kartu Anggota harus diupload',
            'doc_slip_gaji' => 'File Slip Gaji harus diupload',
        ]);

        try {
            DB::beginTransaction();

            $doc_slip_gaji_path = $this->doc_slip_gaji->store('uploads/slip_gaji', 'local');

            $pinjaman = PinjamanModels::create([
                'nomor_pinjaman' => $this->generateNomorTransaksi($this->p_jenis_pinjaman_id),
                'p_anggota_id' => $this->p_anggota_id,
                'p_jenis_pinjaman_id' => $this->p_jenis_pinjaman_id,
                'p_pinjaman_keperluan_ids' => ($this->p_jenis_pinjaman_id == 3) ? '' : $this->p_pinjaman_keperluan_ids,
                'jenis_barang' => ($this->p_jenis_pinjaman_id == 3) ? $this->jenis_barang : null,
                'merk_type' => ($this->p_jenis_pinjaman_id == 3) ? $this->merk_type : null,
                'tenor' => $this->tenor,
                'biaya_admin' => $this->biaya_admin,
                'ra_jumlah_pinjaman' => $this->ra_jumlah_pinjaman,
                'ri_jumlah_pinjaman' => 0,
                'jaminan' => $this->jaminan,
                'jaminan_keterangan' => $this->jaminan_keterangan,
                'jaminan_perkiraan_nilai' => $this->jaminan_perkiraan_nilai,
                'no_rekening' => $this->no_rekening,
                'bank' => $this->bank,
                'tgl_pencairan' => $this->tgl_pencairan,
                'tgl_pelunasan' => $this->tgl_pelunasan,
                // 'doc_ktp' => $doc_ktp_path,
                // 'doc_ktp_suami_istri' => $doc_doc_ktp_suami_istri_path,
                // 'doc_kk' => $doc_kk_path,
                // 'doc_kartu_anggota' => $doc_kartu_anggota_path,
                'doc_slip_gaji' => $doc_slip_gaji_path,
                'p_status_pengajuan_id' => 2, //pending
                'remarks' => $this->remarks,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            if($pinjaman) {
                $redirect = route('main.pinjaman.show', ['id' => $pinjaman]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
                // $this->redirectRoute('main.pinjaman.show', ['id' => $this->id], navigate: true);
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, coba kembali.',
                ]);
            }
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

    function generateNomorTransaksi($jenisPinjaman)
    {
        $kode = 'PJ';
        $bulan = date('n');
        $tahun = date('Y');

        // Konversi bulan ke romawi
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        // Ambil nomor terakhir dari bulan dan tahun ini
        $last = PinjamanModels::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last) {
            // Ambil nomor urut dari string, misalnya "002/PJ/V/2025" → 2
            $lastNomor = (int)substr($last->nomor_pinjaman, 0, 3);
            $nextNomor = str_pad($lastNomor + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNomor = '001';
        }

         $getJenis = JenisPinjamanModels::where('p_jenis_pinjaman_id', $jenisPinjaman)
                                        ->first();

        $inisialJenisPinjaman = $getJenis->kode_jenis_pinjaman;

        // Gabungkan format akhir
        $nomorBaru = $nextNomor . '/' . $kode . '/'. $inisialJenisPinjaman . '/' . $romawi[$bulan] . '/' . $tahun;
        return $nomorBaru;
    }

    public function render()
    {
        return view('livewire.page.main.pinjaman.pinjaman-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
