<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use Illuminate\Database\QueryException;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TagihanModels;
use App\Models\Main\PinjamanModels;
use App\Models\Master\AnggotaModels;
use App\Models\Master\StatusPembayaranModels;
use App\Models\Master\MetodePembayaranModels;

class TagihanCreate extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;
    public $loadData = [];
    public $loadPinjaman = [];
    public $loadStatusPembayaran = [];
    public $loadMetodePembayaran = [];

    #component input
    public $p_anggota_id;
    public $bulan;
    public $tahun;
    public $uraian;
    public $jumlah;
    public $remarks;
    public $t_pinjaman_id;
    public $tgl_jatuh_tempo;
    public $p_status_pembayaran_id;
    public $paid_at;
    public $jumlah_pembayaran;
    public $p_metode_pembayaran_id;

    public function mount() {
        $this->titlePage = 'Tambah Tagihan Anggota';
        $this->menuCode = 'tagihan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tagihan'],
            ['link' => route('main.tagihan.list'), 'label' => 'List'],
            ['link' => route('main.tagihan.create'), 'label' => 'Create']
        ];
        $this->getData();
        $this->getStatusPembayaran();
        $this->getMetodePembayaran();
    }

    public function getData() {
        $data = AnggotaModels::all();
        $this->loadData = $data;
    }

    public function getStatusPembayaran() {
        $data = StatusPembayaranModels::all();
        $this->loadStatusPembayaran = $data;
    }

    public function getMetodePembayaran() {
        $data = MetodePembayaranModels::all();
        $this->loadMetodePembayaran = $data;
    }

    public function updatedPAnggotaId($value)
    {
        $this->getPinjaman($value);
    }

    public function getPinjaman($anggotaId) {
        $data = PinjamanModels::where('p_anggota_id', $anggotaId)
                                ->whereNull('deleted_at')
                                ->whereIn('p_status_pengajuan_id', [5, 6, 7])
                                ->get();
        $this->loadPinjaman = $data;
    }

    public function saveInsert() {
        $validated = $this->validate([
            'p_anggota_id' => 'required',
            't_pinjaman_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'uraian' =>  'required',
            'jumlah' => 'required',
            'p_status_pembayaran_id' => 'required'
        ], [
            'p_anggota_id' => 'Nama Anggota required.',
            't_pinjaman_id' => 'Relasi Pinjaman required.',
            'bulan' => 'Bulan required.',
            'tahun.required' => 'Tahun required.',
            'uraian.required' => 'Uraian required.',
            'jumlah.required' => 'Jumlah required.',
            'p_status_pembayaran_id.required'=> 'Status Pembayaran required.'
        ]);

        try {
            $post = TagihanModels::create([
                'p_anggota_id' => $this->p_anggota_id,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
                'uraian' => $this->uraian,
                'jumlah_tagihan' => $this->jumlah,
                'remarks' => $this->remarks,
                't_pinjaman_id' => $this->t_pinjaman_id,
                'tgl_jatuh_tempo' => $this->tgl_jatuh_tempo,
                'p_status_pembayaran_id' => $this->p_status_pembayaran_id,
                'paid_at' => $this->paid_at,
                'jumlah_pembayaran' => $this->jumlah_pembayaran,
                'p_metode_pembayaran_id' => $this->p_metode_pembayaran_id
            ]);

            if($post) {
                $redirect = route('main.tagihan.show', ['id' => $post]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
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

    public function render()
    {
        return view('livewire.page.main.tagihan.tagihan-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
