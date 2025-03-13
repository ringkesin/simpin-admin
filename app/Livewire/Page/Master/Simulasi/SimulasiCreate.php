<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\SimulasiPinjamanModel;
use App\Traits\MyAlert;

class SimulasiCreate extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    #component input

    public $pinjaman;
    public $margin;
    public $tahun_margin;
    public $tenor;
    public $angsuran;

    public function mount() {
        $this->titlePage = 'Tambah Simulasi Angsuran';
        $this->menuCode = 'master-simulasi';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.simulasi.list'), 'label' => 'Simulasi'],
            ['link' => route('master.simulasi.create'), 'label' => 'Create']
        ];
    }

    public function saveInsert() {
        $validated = $this->validate([
            'pinjaman' => 'required',
            'margin' => 'required',
            'tahun_margin' => 'required',
            'tenor' => 'required',
            'angsuran' => 'required',
        ], [
            'nomor_anggota' => 'Nomor Anggota required',
            'pinjaman.required' => 'Masukkan Jumlah Pinjaman.',
            'margin.required' => 'Bunga Masih kosong.',
            'tahun_margin.required' => 'Tahun Bunga Kosong.',
            'tenor.date' => 'Tenor kosong.',
            'angsuran.required' => 'Angsuran kosong.',
        ]);

        if($this->pinjaman == "") {
            $this->pinjaman = 0;
        }

        try {
            $post = SimulasiPinjamanModel::create([
                'pinjaman' => $this->pinjaman,
                'margin' => $this->margin,
                'tahun_margin' => $this->tahun_margin,
                'tenor' => $this->tenor,
                'angsuran' => $this->angsuran
            ]);

            if($post) {
                $redirect = route('master.simulasi.show', ['id' => $post]);
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
        return view('livewire.page.master.simulasi.simulasi-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
