<?php

namespace App\Livewire\Page\Master\Anggota;

use Livewire\Component;
use App\Models\Master\AnggotaModels;

class AnggotaEdit extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;
    public $nama;
    public $nik;
    public $ktp;
    public $alamat;
    public $tanggal_masuk;
    public $valid_from;
    public $valid_to;

    public function mount($id) {
        $this->titlePage = 'Update Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.anggota.list'), 'label' => 'Anggota'],
            ['link' => route('master.anggota.show', ['id' => $id]), 'label' => 'Show'],
            ['link' => route('master.anggota.edit', ['id' => $id]), 'label' => 'Edit']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = AnggotaModels::find($id);
        $this->loadData = $data;
        $this->nama = $this->loadData['nama'];
        $this->nik = $this->loadData['nik'];
        $this->ktp = $this->loadData['ktp'];
        $this->alamat = $this->loadData['alamat'];
        $this->tanggal_masuk = $this->loadData['tanggal_masuk'];
        $this->valid_from = $this->loadData['valid_from'];
        $this->valid_to = $this->loadData['valid_to'];
    }

    public function render()
    {
        return view('livewire.page.master.anggota.anggota-edit')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
