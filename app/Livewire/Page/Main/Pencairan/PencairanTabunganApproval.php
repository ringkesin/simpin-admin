<?php

namespace App\Livewire\Page\Main\Pencairan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\TabunganPengambilanModels;
use App\Models\Main\TabunganSaldoModels;
use App\Models\Main\TabunganJurnalModels;

class PencairanTabunganApproval extends Component
{
    use MyAlert;
    use MyHelpers;

    public $id;
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $data;

    public $jumlah_disetujui;
    public $tgl_pencairan;
    public $catatan_approver;
    public $status_pencairan;

    public $dataSaldo;
    public $saldoSisa = 0;

    public function mount() {
        $this->data = TabunganPengambilanModels::findOrFail($this->id);

        $this->titlePage = 'Approval Pencairan';
        $this->menuCode = 'pencairan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Pencairan Tabungan'],
            ['link' => route('main.pencairan.list'), 'label' => 'List'],
            ['link' => route('main.pencairan.approval', ['id' => $this->id]), 'label' => 'Approval']
        ];

        $this->status_pencairan = $this->data->status_pengambilan;
        $this->jumlah_disetujui = $this->data->jumlah_disetujui ?? 0;
        $this->tgl_pencairan =  (empty($this->data->tgl_pencairan)) ? null : date('Y-m-d', strtotime($this->data->tgl_pencairan));
        $this->catatan_approver = $this->data->catatan_approver ?? null;

        $this->dataSaldo = TabunganSaldoModels::where('p_jenis_tabungan_id', $this->data->p_jenis_tabungan_id)
            ->where('p_anggota_id', $this->data->p_anggota_id)
            ->orderBy('tahun', 'desc')
            ->first();
        if($this->dataSaldo) {
            $this->saldoSisa = $this->dataSaldo->total_sd;
        }
    }

    public function saveInsert() {
        $this->validate([
            'status_pencairan' => 'required',
            'tgl_pencairan' => 'nullable|date|date_format:Y-m-d',
            'jumlah_disetujui' => 'required|numeric',
            'catatan_approver' =>  'nullable|max:2024',
        ], [
            'status_pencairan.required' => 'Status Pencairan harus diisi.',
            'tgl_pencairan.required' => 'Tgl. Pencairan harus diisi.',
            'jumlah_disetujui.required' => 'Jumlah harus diisi.',
            'catatan_approver.required' => 'Catatan harus diisi.',
        ]);

        try {
            DB::beginTransaction();

            TabunganPengambilanModels::where('t_tabungan_pengambilan_id', $this->id)->update([
                'status_pengambilan' => $this->status_pencairan,
                'tgl_pencairan' => ( ! empty($this->tgl_pencairan)) ? $this->tgl_pencairan.' '.date('H:i:s') : null,
                'jumlah_disetujui' => $this->jumlah_disetujui,
                'catatan_approver' => $this->catatan_approver
            ]);

            if($this->status_pencairan == 'DISETUJUI') {
                TabunganJurnalModels::create([
                    'p_anggota_id' => $this->data->p_anggota_id,
                    'p_jenis_tabungan_id' => $this->data->p_jenis_tabungan_id,
                    'tgl_transaksi' => date('Y-m-d H:i:s'),
                    'nilai' => '-'.$this->jumlah_disetujui,
                    'nilai_sd' => 0,
                    'catatan' => 'Pencairan Tabungan : '.$this->catatan_approver
                ]);
    
                DB::select('SELECT _tabungan_recalculate(:p_anggota_id, :tahun)', [
                    'p_anggota_id' => $this->data->p_anggota_id,
                    'tahun' => date('Y'),
                ]);   
            }

            DB::commit();

            $redirect = route('main.pencairan.approval', ['id' => $this->id]);
            $this->sweetalert([
                'icon' => 'success',
                'confirmButtonText' => 'Okay',
                'showCancelButton' => false,
                'text' => 'Data Berhasil Disimpan !',
                'redirectUrl' => $redirect
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
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
        return view('livewire.page.main.pencairan.pencairan-approval')
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
