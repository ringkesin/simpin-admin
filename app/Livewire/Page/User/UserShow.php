<?php

namespace App\Livewire\Page\User;

use Livewire\Component;
use App\Models\User;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;

class UserShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $id;
    public $loadData = [];

    public function mount($id) {
        $this->titlePage = 'Detail User';
        $this->menuCode = 'user';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'User'],
            ['link' => route('user.list'), 'label' => 'List'],
            ['link' => route('user.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = User::find($id);
        $this->loadData = $data;
    }

    public function checkVal($var, $desiredString  = '-') {
        return $this->setIfNull($var, $desiredString);
    }

    public function render()
    {
        return view('livewire.page.user.user-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
