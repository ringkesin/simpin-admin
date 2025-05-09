<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\SimulasiPinjamanModels;
use App\Models\Master\JenisPinjamanModels;
use App\Traits\MyAlert;

class SimulasiCreate extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $listJenisPinjaman;

    #component input
    public $margin;
    public $tahun_margin;
    public $tenor;
    public $p_jenis_pinjaman_id;

    public function mount() {
        $this->titlePage = 'Tambah Simulasi Angsuran';
        $this->menuCode = 'master-simulasi';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.simulasi.list'), 'label' => 'Simulasi'],
            ['link' => route('master.simulasi.create'), 'label' => 'Create']
        ];
        $this->loadJenisPinjaman();
    }

    public function loadJenisPinjaman()
    {
        $data = JenisPinjamanModels::get();
        $this->listJenisPinjaman = $data;
    }

    public function saveInsert() {
        $validated = $this->validate([
            'p_jenis_pinjaman_id' => 'required',
            'margin' => 'required',
            'tahun_margin' => 'required',
            'tenor' => 'required',
        ], [
            'p_jenis_pinjaman_id.required' => 'Jenis Pinjaman Wajib Diisi',
            'margin.required' => 'Bunga Masih Wajib Diisi.',
            'tahun_margin.required' => 'Tahun Bunga Wajib Diisi.',
            'tenor.date' => 'Tenor Wajib Diisi.',
        ]);

        try {
            $post = SimulasiPinjamanModels::create([
                'p_jenis_pinjaman_id' => $this->p_jenis_pinjaman_id,
                'margin' => $this->margin,
                'tahun_margin' => $this->tahun_margin,
                'tenor' => $this->tenor
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
