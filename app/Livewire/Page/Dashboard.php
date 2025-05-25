<?php

namespace App\Livewire\Page;

use App\Traits\MyHelpers;
use Livewire\Component;
use App\Models\Master\AnggotaModels;
use App\Models\Main\PinjamanModels;
use App\Models\Rbac\RoleUserModel;

class Dashboard extends Component
{
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;
    public $chartData;

    public function mount() {
        $this->titlePage = 'Dashboard';
        $this->menuCode = 'dashboard';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Dashboard']
        ];
        $this->chartPinjamanActive();
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

    public function chartPinjamanActive() {
        $data = PinjamanModels::selectRaw('EXTRACT(MONTH FROM created_at) as bulan, COUNT(*) as total')
                                ->whereYear('created_at', now()->year)
                                ->whereIn('p_status_pengajuan_id', [5, 6, 7])
                                ->groupByRaw('EXTRACT(MONTH FROM created_at)')
                                ->orderByRaw('EXTRACT(MONTH FROM created_at)')
                                ->pluck('total', 'bulan')
                                ->toArray();

        $bulanLabels = [];
        $jumlahPinjaman = [];

        foreach (range(1, 12) as $i) {
            $bulanLabels[] = date("F", mktime(0, 0, 0, $i, 10));
            $jumlahPinjaman[] = $data[$i] ?? 0;
        }

        $this->chartData = [
            'labels' => $bulanLabels,
            'data' => $jumlahPinjaman,
        ];
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

    public function pinjamanPendingTotal() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [2,4])
                                    ->sum('ra_jumlah_pinjaman');

        return $pinjamanCount;
    }

    public function pinjamanAktifTotal() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [5, 6, 7])
                                    ->sum('ri_jumlah_pinjaman');

        return $pinjamanCount;
    }

    public function pinjamanOverdueTotal() {
        $pinjamanCount = PinjamanModels::whereIn('p_status_pengajuan_id', [7])
                                    ->sum('ri_jumlah_pinjaman');

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
