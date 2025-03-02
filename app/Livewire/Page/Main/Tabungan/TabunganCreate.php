<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use Illuminate\Database\QueryException;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TabunganModels;
use App\Models\Master\AnggotaModels;

class TabunganCreate extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;
    public $loadData = [];

    #component input
    public $p_anggota_id;
    public $bulan;
    public $tahun;
    public $simpanan_pokok;
    public $simpanan_wajib;
    public $tabungan_sukarela;
    public $tabungan_indir;
    public $kompensasi_masa_kerja;

    public function mount() {
        $this->titlePage = 'Tambah Tabungan Anggota';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.create'), 'label' => 'Create']
        ];
        $this->getData();
    }

    public function getData() {
        $data = AnggotaModels::all();
        $this->loadData = $data;
    }

    public function saveInsert() {
        $validated = $this->validate([
            'p_anggota_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'simpanan_pokok' =>  'required',
            'simpanan_wajib' => 'required',
            'tabungan_sukarela' => 'required',
            'tabungan_indir' => 'required',
            'kompensasi_masa_kerja' => 'required'
        ], [
            'p_anggota_id' => 'Nama Anggota required',
            'bulan' => 'Bulan required',
            'tahun.required' => 'Tahun required.',
            'simpanan_pokok.required' => 'Simpanan Pokok required.',
            'simpanan_wajib.required' => 'Simpanan Wajib required.',
            'tabungan_sukarela.required' => 'Tabungan Sukarela required.',
            'tabungan_indir.required' => 'Tabungan Indir required.',
            'kompensasi_masa_kerja.required' => 'Kompensasi Masa Kerja required.',
        ]);

        try {
            $post = TabunganModels::create([
                'p_anggota_id' => $this->p_anggota_id,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
                'simpanan_pokok' => $this->simpanan_pokok,
                'simpanan_wajib' => $this->simpanan_wajib,
                'tabungan_sukarela' => $this->tabungan_sukarela,
                'tabungan_indir' => $this->tabungan_indir,
                'kompensasi_masa_kerja' => $this->kompensasi_masa_kerja
            ]);

            if($post) {
                $redirect = route('main.tabungan.show', ['id' => $post]);
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
        return view('livewire.page.main.tabungan.tabungan-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
