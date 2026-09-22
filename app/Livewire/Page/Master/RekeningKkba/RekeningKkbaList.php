<?php

namespace App\Livewire\Page\Master\RekeningKkba;

use Livewire\Component;

class RekeningKkbaList extends Component
{
    public string $titlePage = 'Rekening KKBA';

    public string $menuCode = 'master-rekening-kkba';

    public array $breadcrumb = [];

    public function mount(): void
    {
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.rekening-kkba.list'), 'label' => 'Rekening KKBA'],
            ['link' => null, 'label' => 'List'],
        ];
    }

    public function render()
    {
        return view('livewire.page.master.rekening-kkba.rekening-kkba-list')
            ->layoutData([
                'title' => $this->titlePage,
                'breadcrumbs' => $this->breadcrumb,
                'menu_code' => $this->menuCode,
            ]);
    }
}
