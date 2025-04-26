<?php

namespace App\Livewire\Page\Main\Konten;

use Livewire\Component;

class KontenList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Konten';
        $this->menuCode = 'konten';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Konten'],
            ['link' => route('main.konten.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.konten.konten-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
