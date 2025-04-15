<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Master\AnggotaModels;
use App\Models\Main\TabunganModels;
use Illuminate\Support\Facades\Auth;
use Exception;

class TabunganController extends BaseController
{
    public function getByAnggota(Request $request)
    {
        $user = Auth::user();

        try {
            $anggota = AnggotaModels::where('user_id', $user->id)->first();
            // $tabungan = TabunganModels::where('p_anggota_id', $anggota->p_anggota_id)
            //                         ->whereNull('deleted_at')
            //                         ->get();

            $tabunganPeriod = TabunganModels::where('p_anggota_id', $anggota->p_anggota_id)
                                        ->where('bulan', $request->bulan)
                                        ->where('tahun', $request->tahun)
                                        ->whereNull('deleted_at')
                                        ->first();
            $total_tabungan = 0;

            // foreach($tabunganPeriod as $tabs) {
                $total_tabungan = $total_tabungan
                                    + $tabunganPeriod->simpanan_pokok
                                    + $tabunganPeriod->simpanan_wajib
                                    + $tabunganPeriod->tabungan_sukarela
                                    + $tabunganPeriod->tabungan_indir
                                    + $tabunganPeriod->kompensasi_masa_kerja;
            // }
            $tabunganPeriod->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
            ]);
            $tabunganPeriod->total_tabungan = $total_tabungan;
            return $this->sendResponse($tabunganPeriod, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
