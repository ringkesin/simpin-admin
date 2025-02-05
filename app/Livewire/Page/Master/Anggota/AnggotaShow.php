<?php

namespace App\Livewire\Page\Master\Anggota;

use Livewire\Component;
use App\Models\Master\AnggotaModels;

class AnggotaShow extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail Master Anggota';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.anggota.list'), 'label' => 'Anggota'],
            ['link' => route('master.anggota.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = AnggotaModels::find($id);
        $this->loadData = $data;
    }

    public function render()
    {
        return view('livewire.page.master.anggota.anggota-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
