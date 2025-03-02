<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use App\Models\Main\TagihanModels;
use App\Models\Master\AnggotaModels;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;

class TagihanEdit extends Component
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
    public $uraian;
    public $jumlah;
    public $remarks;

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
    }

    public function getData($id) {
        $data = TagihanModels::find($id);
        $this->loadData = $data;
        $this->p_anggota_id = $this->loadData['p_anggota_id'];
        $this->bulan = $this->loadData['bulan'];
        $this->tahun = $this->loadData['tahun'];
        $this->uraian = $this->loadData['uraian'];
        $this->jumlah = $this->loadData['jumlah'];
        $this->remarks = $this->loadData['remarks'];
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
            'uraian' =>  'required',
            'jumlah' => 'required'
        ], [
            'p_anggota_id' => 'Nama Anggota required',
            'bulan' => 'Bulan required',
            'tahun.required' => 'Tahun required.',
            'uraian.required' => 'Uraian required.',
            'jumlah.required' => 'Jumlah required.'
        ]);

        try {
            $post = TagihanModels::where('t_tagihan_id', $this->id)->update([
                'p_anggota_id' => $this->p_anggota_id,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
                'uraian' => $this->uraian,
                'jumlah' => $this->jumlah,
                'remarks' => $this->remarks
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
