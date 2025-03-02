<?php

namespace App\Livewire\Page\Main\Tagihan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TagihanModels;

class TagihanShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail Tagihan Anggota';
        $this->menuCode = 'tagihan';
        $this->breadcrumb = [
            ['link' => route('main.tagihan.list'), 'label' => 'Tagihan'],
            ['link' => route('main.tagihan.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = TagihanModels::find($id);
        $this->loadData = $data;
    }

    public function render()
    {
        return view('livewire.page.main.tagihan.tagihan-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
