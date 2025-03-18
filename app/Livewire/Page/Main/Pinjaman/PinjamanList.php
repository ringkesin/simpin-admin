<?php

namespace App\Livewire\Page\Main\Pinjaman;

use Livewire\Component;

class PinjamanList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Pinjaman';
        $this->menuCode = 'pinjaman';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Pinjaman'],
            ['link' => route('main.pinjaman.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.pinjaman.pinjaman-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
