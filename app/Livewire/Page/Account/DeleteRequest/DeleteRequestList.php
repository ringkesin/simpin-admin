<?php

namespace App\Livewire\Page\Account\DeleteRequest;

use Livewire\Component;

class DeleteRequestList extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Permintaan Hapus Akun';
        $this->menuCode = 'delete_request_user';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Permintaan Hapus Akun'],
            ['link' => route('account.delete-request.list'), 'label' => 'List']
        ];
    }

    public function render()
    {
        return view('livewire.page.account.delete-request.delete-request-list')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
