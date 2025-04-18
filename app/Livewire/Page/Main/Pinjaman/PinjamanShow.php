<?php

namespace App\Livewire\Page\Main\Pinjaman;

use Livewire\Component;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

use App\Traits\MyAlert;
use App\Traits\MyHelpers;
use App\Models\Main\PinjamanModels;
use App\Models\Master\StatusPengajuanModels;
use App\Models\Master\AnggotaAtributModels;
use App\Models\Master\PinjamanKeperluanModels;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PinjamanShow extends Component
{
    use MyAlert;
    use MyHelpers;

    public $breadcrumb;
    public $titlePage;
    public $menuCode;

    public $loadData = [];
    public $loadDataAttr = [];

    public $listStatus;
    public $id;
    public $ri_jumlah_pinjaman;
    public $p_status_pengajuan_id;

    public function mount($id) {
        $this->titlePage = 'Detail Pinjaman Anggota';
        $this->menuCode = 'pinjaman';
        $this->breadcrumb = [
            ['link' => route('main.pinjaman.list'), 'label' => 'Pinjaman'],
            ['link' => route('main.pinjaman.show', ['id' => $id]), 'label' => 'Show']
        ];

        $this->id = $id;
        $this->getData($id);
    }

    public function getData($id) {
        $data = PinjamanModels::with(['masterAnggota','masterJenisPinjaman'])->find($id);
        $this->loadData = $data;
        $this->ri_jumlah_pinjaman = $this->loadData['ri_jumlah_pinjaman'];
        $this->p_status_pengajuan_id = $this->loadData['p_status_pengajuan_id'];

        $fileUrlDocKtp = null;
        if (Storage::exists($this->loadData['doc_ktp'])) {
            $fileUrlDocKtp = URL::temporarySignedRoute(
                'secure-file', // Route name
                now()->addMinutes(1), // Expiration time
                ['path' => $this->loadData['doc_ktp']] // File path parameter
            );
        }
        $this->loadData['doc_ktp_sec'] = $fileUrlDocKtp;
        $this->loadData['doc_ktp_name'] = basename($this->loadData['doc_ktp']);

        $fileUrlSuamiIstri = null;
        if (Storage::exists($this->loadData['doc_ktp_suami_istri'])) {
            $fileUrlSuamiIstri = URL::temporarySignedRoute(
                'secure-file', // Route name
                now()->addMinutes(1), // Expiration time
                ['path' => $this->loadData['doc_ktp_suami_istri']] // File path parameter
            );
        }
        $this->loadData['doc_ktp_suami_istri_sec'] = $fileUrlSuamiIstri;
        $this->loadData['doc_ktp_suami_istri_name'] = basename($this->loadData['doc_ktp_suami_istri']);

        $fileUrlKK = null;
        if (Storage::exists($this->loadData['doc_kk'])) {
            $fileUrlKK = URL::temporarySignedRoute(
                'secure-file', // Route name
                now()->addMinutes(1), // Expiration time
                ['path' => $this->loadData['doc_kk']] // File path parameter
            );
        }
        $this->loadData['doc_kk_sec'] = $fileUrlKK;
        $this->loadData['doc_kk_name'] = basename($this->loadData['doc_kk']);

        $fileUrlSlipGaji = null;
        if (Storage::exists($this->loadData['doc_slip_gaji'])) {
            $fileUrlSlipGaji = URL::temporarySignedRoute(
                'secure-file', // Route name
                now()->addMinutes(1), // Expiration time
                ['path' => $this->loadData['doc_slip_gaji']] // File path parameter
            );
        }
        $this->loadData['doc_slip_gaji_sec'] = $fileUrlSlipGaji;
        $this->loadData['doc_slip_gaji_name'] = basename($this->loadData['doc_slip_gaji']);

        $fileUrlIDCardPegawai = null;
        if (Storage::exists($this->loadData['doc_kartu_anggota'])) {
            $fileUrlIDCardPegawai = URL::temporarySignedRoute(
                'secure-file', // Route name
                now()->addMinutes(1), // Expiration time
                ['path' => $this->loadData['doc_kartu_anggota']] // File path parameter
            );
        }
        $this->loadData['doc_id_card_pegawai'] = $fileUrlIDCardPegawai;

        $attribute = [];
        foreach($this->loadData['p_pinjaman_keperluan_ids'] as $d){
            $keperluanValue = PinjamanKeperluanModels::find($d);
            // dd($keperluanValue);
            $attribute[] = $keperluanValue->keperluan;
        }
        $this->loadData['keperluan'] = $attribute;
        $this->loadData = $this->loadData->toArray();

        $this->getDataAttr($data['p_anggota_id']);
    }

    public function getDataAttr($id) {
        $data = AnggotaAtributModels::where('p_anggota_id', '=', $id)->get();
        // $this->loadDataAttr = $data;
        $attribute = [];
        foreach($data as $d){

            $fileUrl = null;
            if (Storage::exists($d->atribut_attachment)) {
                $fileUrl = URL::temporarySignedRoute(
                    'secure-file', // Route name
                    now()->addMinutes(1), // Expiration time
                    ['path' => $d->atribut_attachment] // File path parameter
                );
            }

            $attribute[] = [
                'atribut_kode' => $d->atribut_kode_beautify,
                'atribut_value' => $d->atribut_value,
                'atribut_attachment' => $fileUrl
            ];
        }
        $this->loadDataAttr = $attribute;
    }

    public function listStatusPinjaman() {
        $data = StatusPengajuanModels::all();
        return $data;
    }

    public function saveApproval() {
        $validated = $this->validate([
            'ri_jumlah_pinjaman' => 'required',
            'p_status_pengajuan_id' => 'required'
        ], [
            'ri_jumlah_pinjaman' => 'Jumlah Pinjaman yang Disetujui required',
            'p_status_pengajuan_id.required' => 'Status Pengajuan required.'
        ]);

        try {
            $post = PinjamanModels::where('t_pinjaman_id', $this->id)->update([
                'ri_jumlah_pinjaman' => $this->ri_jumlah_pinjaman,
                'p_status_pengajuan_id' => $this->p_status_pengajuan_id,
            ]);

            if($post) {
                $redirect = route('main.pinjaman.show', ['id' => $this->id]);
                $this->sweetalert([
                    'icon' => 'success',
                    'confirmButtonText' => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data Berhasil Disimpan !',
                    'redirectUrl' => $redirect
                ]);
                // $this->redirectRoute('main.pinjaman.show', ['id' => $this->id], navigate: true);
            } else {
                $this->sweetalert([
                    'icon' => 'warning',
                    'confirmButtonText'  => 'Okay',
                    'showCancelButton' => false,
                    'text' => 'Data gagal di update, coba kembali.',
                ]);
            }
        } catch (QueryException $e) {
            $textError = '';
            if($e->errorInfo[1] == 1062) {
                $textError = 'Data gagal di update karena duplikat data, coba kembali.';
            } else {
                $textError = 'Data gagal di update, coba kembali.';
            }
            $this->sweetalert([
                'icon' => 'error',
                'confirmButtonText'  => 'Okay',
                'showCancelButton' => false,
                'text' => $textError,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.page.main.pinjaman.pinjaman-show')
        ->layoutData([
            'title' => $this->titlePage, //Page Title
            'breadcrumbs' => $this->breadcrumb,
            'menu_code' => $this->menuCode
        ]);
    }
}
