<?php

namespace App\Livewire\Page\Main\Tabungan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use Livewire\Attributes\Computed;
use App\Models\Main\TabunganModels;
use App\Models\Master\AnggotaModels;
use App\Models\Main\TabunganJurnalModels;
use App\Models\Main\TabunganSaldoModels;
use Illuminate\Support\Facades\Auth;

class TabunganUpdate extends Component
{
    use MyAlert;
    use MyHelpers;

    public $id;
    public $breadcrumb;
    public $titlePage;
    public $menuCode;
    
    public $dataAnggota;
    public $tahun;
    public array $items = [
        ['nilai' => 1],
        ['nilai' => 2],
        ['nilai' => 3],
        ['nilai' => 4],
        ['nilai' => 5],
        ['nilai' => 6],
        ['nilai' => 7],
        ['nilai' => 8],
        ['nilai' => 9],
        ['nilai' => 10],
        ['nilai' => 11],
        ['nilai' => 12],
    ];

    public function mount() {
        $this->fetch_anggota();
        $this->tahun = date('Y');

        $this->titlePage = 'Tabungan : '.$this->dataAnggota->nama;
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.update', ['id' => $this->id]), 'label' => 'Tabungan : '.$this->dataAnggota->nama]
        ];
    }

    #[Computed]
    function fetch_anggota()
    {
        $this->dataAnggota = AnggotaModels::findOrFail($this->id);
    }

    public function render()
    {
        return view('livewire.page.main.tabungan.tabungan-update')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }

    function exception($e, $stopPropagation)
    {
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            $message = 'Mohon lengkapi form yang masih merah.';
        }
        else{
            $message = $e->getMessage();
            DB::rollBack();
        }
        
        $this->sweetalert([
            'icon' => 'error',
            'confirmButtonText'  => 'Okay',
            'showCancelButton' => false,
            'text' => $message,
        ]);
        $stopPropagation();
    }
}
