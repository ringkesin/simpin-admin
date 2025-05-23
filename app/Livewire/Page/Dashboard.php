<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Master\AnggotaModels;
use App\Models\Main\PinjamanModels;
use App\Models\Rbac\RoleUserModel;

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
                                    ->where('is_registered', TRUE)
                                    ->count();

        return $anggotaCount;
    }

    public function anggotaUnregisteredCount() {
        $anggotaCount = AnggotaModels::where('is_registered', FALSE)
                                    ->count();

        return $anggotaCount;
    }

    public function userMobileAnggotaCount() {
        $user = RoleUserModel::where(function ($query) {
                                        $query->whereNull('valid_until')
                                            ->orWhere('valid_until', '<', now());
                                    })
                                    ->where('valid_from', '<=', now())
                                    ->where('role_id', 4)
                                    ->count();

        return $user;
    }

    public function pinjamanPendingCount() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [2, 4])
                                    ->count();

        return $pinjamanCount;
    }

    public function pinjamanAktifCount() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [5, 6, 7])
                                    ->count();

        return $pinjamanCount;
    }

    public function pinjamanOverdueCount() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [7])
                                    ->count();

        return $pinjamanCount;
    }

    public function pinjamanDoneCount() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [8])
                                    ->count();

        return $pinjamanCount;
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
