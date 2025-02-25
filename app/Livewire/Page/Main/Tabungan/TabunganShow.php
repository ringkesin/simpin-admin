<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TabunganModels;

class TabunganShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail Tabungan Anggota';
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => route('main.tabungan.list'), 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = TabunganModels::find($id);
        $this->loadData = $data;
    }

    public function render()
    {
        return view('livewire.page.main.tabungan.tabungan-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
