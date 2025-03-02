<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use App\Models\Main\TabunganModels;
use App\Models\Master\AnggotaModels;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;

class TabunganEdit extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData = [];
    public $loadDataAnggota = [];

    #component input
    public $id;
    public $p_anggota_id;
    public $bulan;
    public $tahun;
    public $simpanan_pokok;
    public $simpanan_wajib;
    public $tabungan_sukarela;
    public $tabungan_indir;
    public $kompensasi_masa_kerja;

    public function mount($id) {
        $this->titlePage = 'Update Tabungan Anggota';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('main.tabungan.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
        $this->getAnggota();
    }

    public function getData($id) {
        $data = TabunganModels::find($id);
        $this->loadData = $data;
        $this->p_anggota_id = $this->loadData['p_anggota_id'];
        $this->bulan = $this->loadData['bulan'];
        $this->tahun = $this->loadData['tahun'];
        $this->simpanan_pokok = $this->loadData['simpanan_pokok'];
        $this->simpanan_wajib = $this->loadData['simpanan_wajib'];
        $this->tabungan_sukarela = $this->loadData['tabungan_sukarela'];
        $this->tabungan_indir = $this->loadData['tabungan_indir'];
        $this->kompensasi_masa_kerja = $this->loadData['kompensasi_masa_kerja'];
    }

    public function getAnggota() {
        $data = AnggotaModels::all();
        $this->loadDataAnggota = $data;
    }

    public function saveUpdate() {
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
            $post = TabunganModels::where('t_tabungan_id', $this->id)->update([
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
                $redirect = route('main.tabungan.show', ['id' => $this->id]);
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
        return view('livewire.page.main.tabungan.tabungan-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
