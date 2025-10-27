<?php

namespace App\Livewire\Page\Main\PerubahanPenyertaan;

use Livewire\Component;

class PerubahanPenyertaanTabunganList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Request Perubahan Penyertaan Tabungan';
        $this->menuCode = 'perubahan-penyertaan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Perubahan Penyertaan Tabungan'],
            ['link' => route('main.perubahan-penyertaan.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.perubahan-penyertaan.perubahan-penyertaan-tabungan-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
