<?php

namespace App\Livewire\Page\Main\Shu;

use Livewire\Component;

class ShuList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'SHU';
        $this->menuCode = 'shu';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'SHU'],
            ['link' => route('main.shu.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.shu.shu-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
