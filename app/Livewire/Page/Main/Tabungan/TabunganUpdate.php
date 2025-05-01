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
use App\Models\Master\JenisTabunganModels;
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

    public $tglTransaksi;
    public $jenisTabunganId;
    public $jumlah;
    public $remarks;

    public $dataAnggota;
    public $dataJenisTabungan;

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
        $this->fetch_jenis_tabungan();
        $this->tahun = date('Y');

        $this->titlePage = 'Tabungan : '.$this->dataAnggota->nama;
        $this->menuCode = 'tabungan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Tabungan'],
            ['link' => route('main.tabungan.list'), 'label' => 'List'],
            ['link' => route('main.tabungan.update', ['id' => $this->id]), 'label' => 'Tabungan : '.$this->dataAnggota->nama]
        ];

        $this->tglTransaksi = date('Y-m-d H:i');
    }

    #[Computed]
    function fetch_anggota()
    {
        $this->dataAnggota = AnggotaModels::findOrFail($this->id);
    }

    #[Computed]
    function fetch_jenis_tabungan()
    {
        $this->dataJenisTabungan = JenisTabunganModels::all();
    }

    public function saveInsert() {
        $validated = $this->validate([
            'tglTransaksi' => 'required',
            'jenisTabunganId' => 'required',
            'jumlah' => 'required|numeric',
            'remarks' =>  'nullable|max:2024',
        ], [
            'tglTransaksi.required' => 'Tanggal Transaksi harus diisi.',
            'jenisTabunganId.required' => 'Jenis Tabungan harus diisi.',
            'jumlah.required' => 'Jumlah harus diisi.',
            'remarks.required' => 'Catatan harus diisi.',
        ]);

        try {
            $post = TabunganJurnalModels::create([
                'p_anggota_id' => $this->id,
                'p_jenis_tabungan_id' => $this->jenisTabunganId,
                'tgl_transaksi' => $this->tglTransaksi,
                'nilai' => $this->jumlah,
                'nilai_sd' => $this->jumlah,
                'remarks' => $this->remarks
            ]);
            if($post) {
                $redirect = route('main.tabungan.update', ['id' => $this->id]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, coba kembali.',
                ]);
            }
        } catch (QueryException $e) {
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $e->getMessage()
            ]);
        }
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
