<?php

namespace App\Livewire\Page\Tabungan;

use Livewire\Component;

class TabunganList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Tabungan';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('tabungan.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.tabungan.tabungan-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
