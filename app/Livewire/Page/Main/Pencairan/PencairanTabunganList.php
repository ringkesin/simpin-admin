<?php

namespace App\Livewire\Page\Main\Pencairan;

use Livewire\Component;

class PencairanTabunganList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Request Pencairan Tabungan';
        $this->menuCode = 'pencairan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Pencairan Tabungan'],
            ['link' => route('main.pencairan.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.pencairan.pencairan-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
