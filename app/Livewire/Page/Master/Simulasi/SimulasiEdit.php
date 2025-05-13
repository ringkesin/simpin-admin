<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\AnggotaModels;
use App\Models\Master\SimulasiPinjamanModels;
use App\Models\Master\JenisPinjamanModels;
use App\Traits\MyAlert;

class SimulasiEdit extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $listJenisPinjaman;
    public $loadData;
    public $id;
    public $tenor;
    public $margin;
    public $tahun_margin;
    public $p_jenis_pinjaman_id;
    public $biaya_admin;

    public function mount($id) {
        $this->titlePage = 'Update Simulasi Angsuran';
        $this->menuCode = 'master-simulasi';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.simulasi.list'), 'label' => 'Simulasi'],
            ['link' => route('master.simulasi.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('master.simulasi.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
        $this->loadJenisPinjaman();
    }

    public function loadJenisPinjaman()
    {
        $data = JenisPinjamanModels::get();
        $this->listJenisPinjaman = $data;
    }

    public function getData($id) {
        $data = SimulasiPinjamanModels::find($id);
        $this->loadData = $data;
        $this->p_jenis_pinjaman_id = $this->loadData['p_jenis_pinjaman_id'];
        $this->tenor = $this->loadData['tenor'];
        $this->margin = $this->loadData['margin'];
        $this->tahun_margin = $this->loadData['tahun_margin'];
        $this->biaya_admin = $this->loadData['biaya_admin'];
    }

    public function saveUpdate() {
        $validated = $this->validate([
            'p_jenis_pinjaman_id' => 'required',
            'margin' => 'required',
            'tahun_margin' => 'required',
            'tenor' => 'required',
            'biaya_admin' => 'required'
        ], [
            'p_jenis_pinjaman_id.required' => 'Jenis Pinjaman Wajib Diisi',
            'margin.required' => 'Bunga Masih Wajib Diisi.',
            'tahun_margin.required' => 'Tahun Bunga Wajib Diisi.',
            'tenor.date' => 'Tenor Wajib Diisi.',
            'biaya_admin.required' => 'Biaya Admin Wajib Diisi.'
        ]);

        $redirect = route('master.simulasi.list');

        try {
            $post = SimulasiPinjamanModels::where('id', $this->id)->update([
                'p_jenis_pinjaman_id' => $this->p_jenis_pinjaman_id,
                'margin' => $this->margin,
                'tahun_margin' => $this->tahun_margin,
                'tenor' => $this->tenor,
                'biaya_admin' => $this->biaya_admin
            ]);

            if($post) {
                $redirect = route('master.simulasi.show', ['id' => $this->id]);
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
        return view('livewire.page.master.simulasi.simulasi-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
