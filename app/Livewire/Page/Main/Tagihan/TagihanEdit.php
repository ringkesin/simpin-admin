<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TagihanModels;
use App\Models\Main\PinjamanModels;
use App\Models\Master\AnggotaModels;
use App\Models\Master\StatusPembayaranModels;
use App\Models\Master\MetodePembayaranModels;

class TagihanEdit extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData = [];
    public $loadDataAnggota = [];
    public $loadPinjaman = [];
    public $loadStatusPembayaran = [];
    public $loadMetodePembayaran = [];

    #component input
    public $id;
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

    public function mount($id) {
        $this->titlePage = 'Update Tagihan Anggota';
        $this->menuCode = 'tagihan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tagihan'],
            ['link' => route('main.tagihan.list'), 'label' => 'List'],
            ['link' => route('main.tagihan.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('main.tagihan.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
        $this->getAnggota();
        $this->getStatusPembayaran();
        $this->getMetodePembayaran();
    }

    public function getData($id) {
        $data = TagihanModels::find($id);
        $this->loadData = $data;
        $this->p_anggota_id = $this->loadData['p_anggota_id'];
        $this->bulan = $this->loadData['bulan'];
        $this->tahun = $this->loadData['tahun'];
        $this->uraian = $this->loadData['uraian'];
        $this->jumlah = $this->loadData['jumlah_tagihan'];
        $this->remarks = $this->loadData['remarks'];
        $this->t_pinjaman_id = $this->loadData['t_pinjaman_id'];
        $this->tgl_jatuh_tempo = $this->loadData['tgl_jatuh_tempo'];
        $this->p_status_pembayaran_id = $this->loadData['p_status_pembayaran_id'];
        $this->paid_at = $this->loadData['paid_at'];
        $this->jumlah_pembayaran = $this->loadData['jumlah_pembayaran'];
        $this->p_metode_pembayaran_id = $this->loadData['p_metode_pembayaran_id'];

        $this->getPinjaman($this->p_anggota_id);
    }

    public function getAnggota() {
        $data = AnggotaModels::all();
        $this->loadDataAnggota = $data;
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

    public function saveUpdate() {
        $validated = $this->validate([
            'p_anggota_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'uraian' =>  'required',
            'jumlah' => 'required',
            'p_status_pembayaran_id' => 'required'
        ], [
            'p_anggota_id' => 'Nama Anggota required',
            'bulan' => 'Bulan required',
            'tahun.required' => 'Tahun required.',
            'uraian.required' => 'Uraian required.',
            'jumlah.required' => 'Jumlah required.',
            'p_status_pembayaran_id.required'=> 'Status Pembayaran required.'
        ]);

        try {
            $post = TagihanModels::where('t_tagihan_id', $this->id)->update([
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
                $redirect = route('main.tagihan.show', ['id' => $this->id]);
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
        return view('livewire.page.main.tagihan.tagihan-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
