<?php

namespace App\Livewire\Page\Main\Penyertaan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;

use App\Models\Main\TabunganPenyertaanModels;

class PenyertaanTabunganApproval extends Component
{
    use MyAlert;
    use MyHelpers;

    public $id;
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $data;

    public $jumlah_disetujui;
    public $penyertaan_date;
    public $catatan_approver;
    public $status_penyertaan;

    public function mount() {
        $this->data = TabunganPenyertaanModels::findOrFail($this->id);

        $this->titlePage = 'Approval Penyertaan';
        $this->menuCode = 'penyertaan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Penyertaan Tabungan'],
            ['link' => route('main.penyertaan.list'), 'label' => 'List'],
            ['link' => route('main.penyertaan.approval', ['id' => $this->id]), 'label' => 'Approval']
        ];

        $this->status_penyertaan = $this->data->status_penyertaan;
        $this->jumlah_disetujui = $this->data->jumlah ?? 0;
        $this->penyertaan_date =  (empty($this->data->penyertaan_date)) ? null : date('Y-m-d', strtotime($this->data->penyertaan_date));
        $this->catatan_approver = $this->data->catatan_approver ?? null;
    }

    public function exportPDF()
    {
        // $data = $this->only(array_keys(get_object_vars($this)));
        $data =  [];
        $data = [
            'nama_anggota' => $this->data->masterAnggota->nama,
            'nomor_anggota' => $this->data->masterAnggota->nomor_anggota,
            'nik' => $this->data->masterAnggota->nik,
            'mobile' => $this->data->masterAnggota->mobile,
            'jumlah_penyertaan' => $this->data->jumlah,
            'terbilang_penyertaan' => $this->data->jumlah ? $this->toTerbilang($this->data->jumlah) : null,
            'tgl_penyertaan' => date('d F Y', strtotime($this->data->penyertaan_date)),
            'tgl_pengajuan' => date('d F Y', strtotime($this->data->created_at))
        ];

        $pdf = Pdf::loadView('livewire.page.main.export.tabungan-formulir-export', $data)
                  ->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'Formulir_Pencairan_KKBA.pdf'
        );
    }

    public function saveInsert() {
        $this->validate([
            'status_penyertaan' => 'required',
            'penyertaan_date' => 'nullable|date|date_format:Y-m-d',
            'jumlah_disetujui' => 'required|numeric',
            'catatan_approver' =>  'required|max:2024',
        ], [
            'status_penyertaan.required' => 'Status Penyertaan harus diisi.',
            'penyertaan_date.required' => 'Tgl. Penyertaan harus diisi.',
            'jumlah_disetujui.required' => 'Jumlah harus diisi.',
            'catatan_approver.required' => 'Catatan harus diisi.',
        ]);

        try {
            DB::beginTransaction();

            TabunganPenyertaanModels::where('t_tabungan_penyertaan_id', $this->id)->update([
                'status_penyertaan' => $this->status_penyertaan,
                'penyertaan_date' => ( ! empty($this->penyertaan_date)) ? $this->penyertaan_date.' '.date('H:i:s') : null,
                'jumlah' => $this->jumlah_disetujui,
                'catatan_approver' => $this->catatan_approver
            ]);

            DB::commit();

            $redirect = route('main.penyertaan.approval', ['id' => $this->id]);
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
        return view('livewire.page.main.penyertaan.penyertaan-tabungan-approval')
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
