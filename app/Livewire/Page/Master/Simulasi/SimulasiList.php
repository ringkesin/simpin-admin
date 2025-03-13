<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;

class SimulasiList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Simulasi Pinjaman';
        $this->menuCode = 'master-simulasi';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => null, 'label' => 'Simulasi'],
            ['link' => route('master.simulasi.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.master.simulasi.simulasi-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
