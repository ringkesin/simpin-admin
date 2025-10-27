<?php

namespace App\Livewire\Page\Main\Penyertaan;

use Livewire\Component;

class PenyertaanTabunganList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Request Penyertaan Tabungan';
        $this->menuCode = 'penyertaan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Penyertaan Tabungan'],
            ['link' => route('main.penyertaan.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.penyertaan.penyertaan-tabungan-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
