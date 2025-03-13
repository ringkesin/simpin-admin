<?php

namespace App\Livewire\Page\Master\Simulasi;

use Livewire\Component;
use Illuminate\Database\QueryException;
use App\Models\Master\AnggotaModels;
use App\Models\Master\SimulasiPinjamanModel;
use App\Models\User;
use App\Models\Rbac\RoleUserModel;
use Illuminate\Support\Facades\Hash;
use App\Traits\MyAlert;

class SimulasiShow extends Component
{
    use MyAlert;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData;
    public $id;
    public $tglLahir;
    public $pinjaman;

    public function mount($id) {
        $this->titlePage = 'Detail Simulasi Angsuran';
        $this->menuCode = 'master-anggota';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Master'],
            ['link' => route('master.simulasi.list'), 'label' => 'Simulasi'],
            ['link' => route('master.simulasi.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = SimulasiPinjamanModel::find($id);
        $this->loadData = $data;
        $this->tglLahir = str_replace("-", "", $this->loadData['tgl_lahir']);
        $this->pinjaman = number_format($this->loadData['pinjaman']);
    }


    public function render()
    {
        return view('livewire.page.master.simulasi.simulasi-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
