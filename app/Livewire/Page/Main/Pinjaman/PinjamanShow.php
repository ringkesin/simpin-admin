<?php

namespace App\Livewire\Page\Main\Pinjaman;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\PinjamanModels;

class PinjamanShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;

    public function mount($id) {
        $this->titlePage = 'Detail Pinjaman Anggota';
        $this->menuCode = 'pinjaman';
        $this->breadcrumb = [
            ['link' => route('main.pinjaman.list'), 'label' => 'Pinjaman'],
            ['link' => route('main.pinjaman.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = PinjamanModels::find($id);
        $this->loadData = $data;
    }

    public function render()
    {
        return view('livewire.page.main.pinjaman.pinjaman-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
