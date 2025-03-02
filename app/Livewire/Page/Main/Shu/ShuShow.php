<?php

namespace App\Livewire\Page\Main\Shu;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\ShuModels;

class ShuShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail SHU Anggota';
        $this->menuCode = 'shu';
        $this->breadcrumb = [
            ['link' => route('main.shu.list'), 'label' => 'SHU'],
            ['link' => route('main.shu.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = ShuModels::find($id);
        $this->loadData = $data;
    }

    public function render()
    {
        return view('livewire.page.main.shu.shu-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
