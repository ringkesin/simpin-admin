<?php

namespace App\Livewire\Page\Main\PerubahanPenyertaan;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;

use App\Models\Main\TabunganPerubahanPenyertaanModels;

class PerubahanPenyertaanTabunganApproval extends Component
{
    use MyAlert;
    use MyHelpers;

    public $id;
    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $data;

    public $nilai_sebelum;
    public $nilai_baru;
    public $valid_from;
    public $catatan_approver;
    public $status_perubahan_penyertaan;

    public function mount() {
        $this->data = TabunganPerubahanPenyertaanModels::findOrFail($this->id);

        $this->titlePage = 'Approval Perubahan Penyertaan';
        $this->menuCode = 'perubahan-penyertaan';
        $this->breadcrumb = [
            ['link' => null, 'label' => 'Perubahan Penyertaan Tabungan'],
            ['link' => route('main.perubahan-penyertaan.list'), 'label' => 'List'],
            ['link' => route('main.perubahan-penyertaan.approval', ['id' => $this->id]), 'label' => 'Approval']
        ];

        $this->status_perubahan_penyertaan = $this->data->status_perubahan_penyertaan;
        $this->nilai_sebelum = $this->data->nilai_sebelum ?? 0;
        $this->nilai_baru = $this->data->nilai_baru ?? 0;
        $this->valid_from =  (empty($this->data->valid_from)) ? null : date('Y-m-d', strtotime($this->data->valid_from));
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
            'nilai_sebelum' => $this->data->nilai_sebelum,
            'nilai_baru' => $this->data->nilai_baru,
            'valid_from' => date('d F Y', strtotime($this->data->valid_from)),
            'tgl_pengajuan' => date('d F Y', strtotime($this->data->created_at))
        ];

        $pdf = Pdf::loadView('livewire.page.main.export.tabungan-formulir-export', $data)
                  ->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'Formulir_Perubahan_Penyertaan_KKBA_'.$this->data->masterAnggota->nomor_anggota.'.pdf'
        );
    }

    public function saveInsert() {
        $this->validate([
            'status_perubahan_penyertaan' => 'required',
            'valid_from' => 'nullable|date|date_format:Y-m-d',
            'nilai_sebelum' => 'required|numeric',
            'nilai_baru' => 'required|numeric',
            'catatan_approver' =>  'required|max:2024',
        ], [
            'status_perubahan_penyertaan.required' => 'Status Perubahan Penyertaan harus diisi.',
            'valid_from.required' => 'Valid Dari harus diisi.',
            'nilai_sebelum.required' => 'Nilai Sebelum harus diisi.',
            'nilai_baru.required' => 'Nilai Baru harus diisi',
            'catatan_approver.required' => 'Catatan harus diisi.',
        ]);

        try {
            DB::beginTransaction();

            TabunganPerubahanPenyertaanModels::where('t_tabungan_perubahan_penyertaan_id', $this->id)->update([
                'status_perubahan_penyertaan' => $this->status_perubahan_penyertaan,
                'valid_from' => ( ! empty($this->valid_from)) ? $this->valid_from.' '.date('H:i:s') : null,
                'nilai_sebelum' => $this->nilai_sebelum,
                'nilai_baru' => $this->nilai_baru,
                'catatan_approver' => $this->catatan_approver
            ]);

            DB::commit();

            $redirect = route('main.perubahan-penyertaan.approval', ['id' => $this->id]);
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
        return view('livewire.page.main.perubahan-penyertaan.perubahan-penyertaan-tabungan-approval')
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
