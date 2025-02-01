<?php

namespace App\Livewire\Page\Master\Anggota;

use Livewire\Component;

class AnggotaList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => null, 'label' => 'Anggota'],
            ['link' => route('master.anggota.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.master.anggota.anggota-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
