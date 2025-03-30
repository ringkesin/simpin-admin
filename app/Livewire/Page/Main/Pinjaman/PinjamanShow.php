<?php

namespace App\Livewire\Page\Main\Pinjaman;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\PinjamanModels;
use App\Models\Master\StatusPengajuanModels;
use App\Models\Master\AnggotaAtributModels;

class PinjamanShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData = [];
    public $loadDataAttr = [];

    public $listStatus;
    public $id;
    public $ri_jumlah_pinjaman;
    public $prakiraan_nilai_pasar;
    public $p_status_pengajuan_id;

    public function mount($id) {
        $this->titlePage = 'Detail Pinjaman Anggota';
        $this->menuCode = 'pinjaman';
        $this->breadcrumb = [
            ['link' => route('main.pinjaman.list'), 'label' => 'Pinjaman'],
            ['link' => route('main.pinjaman.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = PinjamanModels::find($id);
        $this->loadData = $data;
        $this->ri_jumlah_pinjaman = $this->loadData['ri_jumlah_pinjaman'];
        $this->prakiraan_nilai_pasar = $this->loadData['prakiraan_nilai_pasar'];
        $this->p_status_pengajuan_id = $this->loadData['p_status_pengajuan_id'];
        $this->getDataAttr($data['p_anggota_id']);
    }

    public function getDataAttr($id) {
        $data = AnggotaAtributModels::where('p_anggota_id', '=', $id)->get();
        $this->loadDataAttr = $data;
    }

    public function listStatusPinjaman() {
        $data = StatusPengajuanModels::all();
        return $data;
    }

    public function saveApproval() {
        $validated = $this->validate([
            'ri_jumlah_pinjaman' => 'required',
            'prakiraan_nilai_pasar' => 'required',
            'p_status_pengajuan_id' => 'required'
        ], [
            'ri_jumlah_pinjaman' => 'Jumlah Pinjaman yang Disetujui required',
            'prakiraan_nilai_pasar' => 'Prakiraan Nilai Pasar required',
            'p_status_pengajuan_id.required' => 'Status Pengajuan required.'
        ]);

        try {
            $post = PinjamanModels::where('t_pinjaman_id', $this->id)->update([
                'ri_jumlah_pinjaman' => $this->ri_jumlah_pinjaman,
                'prakiraan_nilai_pasar' => $this->prakiraan_nilai_pasar,
                'p_status_pengajuan_id' => $this->p_status_pengajuan_id,
            ]);

            if($post) {
                $redirect = route('main.pinjaman.show', ['id' => $this->id]);
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
        return view('livewire.page.main.pinjaman.pinjaman-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
