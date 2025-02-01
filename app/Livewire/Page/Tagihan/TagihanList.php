<?php

namespace App\Livewire\Page\Tagihan;

use Livewire\Component;

class TagihanList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Tagihan';
        $this->menuCode = 'tagihan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tagihan'],
            ['link' => route('tagihan.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.tagihan.tagihan-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
