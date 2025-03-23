<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Master\AnggotaModels;

class Dashboard extends Component
{
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public function mount() {
        $this->titlePage = 'Dashboard';
        $this->menuCode = 'dashboard';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Dashboard']
        ];
    }

    public function anggotaCount() {
        $anggotaCount = AnggotaModels::where(function ($query) {
                                        $query->whereNull('valid_to')
                                            ->orWhere('valid_to', '<', now());
                                    })
                                    ->where('valid_from', '<=', now())
                                    ->count();

        return $anggotaCount;
    }

    public function render()
    {
        return view('livewire.page.dashboard')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
