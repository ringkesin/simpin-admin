<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\AnggotaModels;
use App\Models\Master\SimulasiPinjamanModel;
use App\Traits\MyAlert;

class SimulasiEdit extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;
    public $pinjaman;
    public $tenor;
    public $margin;
    public $angsuran;
    public $tahun_margin;


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
    }

    public function getData($id) {
        $data = SimulasiPinjamanModel::find($id);
        $this->loadData = $data;
        $this->pinjaman = $this->loadData['pinjaman'];
        $this->tenor = $this->loadData['tenor'];
        $this->margin = $this->loadData['margin'];
        $this->tahun_margin = $this->loadData['tahun_margin'];
        $this->angsuran = $this->loadData['angsuran'];

    }

    public function saveUpdate() {
        $validated = $this->validate([
            'pinjaman' => 'required',
            'margin' => 'required',
            'tahun_margin' => 'required',
            'tenor' => 'required',
            'angsuran' => 'required',
        ], [
            'pinjaman.required' => 'Masukkan Jumlah Pinjaman.',
            'margin.required' => 'Bunga Masih kosong.',
            'tahun_margin.required' => 'Tahun Bunga Kosong.',
            'tenor.date' => 'Tenor kosong.',
        ]);
        if($this->pinjaman == "") {
            $this->pinjaman = null;
        }

        $redirect = route('master.simulasi.list');

        try {
            $post = SimulasiPinjamanModel::where('id', $this->id)->update([
                'pinjaman' => $this->pinjaman,
                'margin' => $this->margin,
                'tahun_margin' => $this->tahun_margin,
                'tenor' => $this->tenor,
                'angsuran' => $this->angsuran
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
