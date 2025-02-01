<?php

namespace App\Livewire\Page\User;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'User';
        $this->menuCode = 'user';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'User'],
            ['link' => null, 'label' => 'List'],
        ];
    }

    public function render()
    {
        return view('livewire.page.user.user-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
