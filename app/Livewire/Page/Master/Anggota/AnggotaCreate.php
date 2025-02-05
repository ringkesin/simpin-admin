<?php

namespace App\Livewire\Page\Master\Anggota;

use Livewire\Component;

class AnggotaCreate extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    #component input
    public $nama;

    public function mount() {
        $this->titlePage = 'Tambah Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.anggota.list'), 'label' => 'Anggota'],
            ['link' => route('master.anggota.create'), 'label' => 'Create']
        ];
    }

    public function render()
    {
        return view('livewire.page.master.anggota.anggota-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
