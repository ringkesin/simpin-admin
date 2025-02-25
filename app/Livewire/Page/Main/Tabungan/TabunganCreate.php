<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use Illuminate\Database\QueryException;

use App\Traits\MyAlert;
use App\Models\Main\TabunganModels;

class TabunganCreate extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Tambah Tabungan Anggota';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.create'), 'label' => 'Create']
        ];
    }

    public function render()
    {
        return view('livewire.page.main.tabungan.tabungan-create')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
