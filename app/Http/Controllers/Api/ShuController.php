<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Master\AnggotaModels;
use App\Models\Main\ShuModels;
use Illuminate\Support\Facades\Auth;
use Exception;

class ShuController extends BaseController
{
    public function getByAnggota(Request $request)
    {
        $user = Auth::user();

        try {
            $anggota = AnggotaModels::where('user_id', $user->id)->first();

            $shuPeriod = ShuModels::where('p_anggota_id', $anggota->p_anggota_id)
                                        ->where('tahun', $request->tahun)
                                        ->whereNull('deleted_at')
                                        ->first();
            $total_shu = 0;

            $total_shu = $total_shu
                                + $shuPeriod->shu_diterima
                                + $shuPeriod->shu_diterima
                                + $shuPeriod->shu_ditabung
                                + $shuPeriod->shu_tahun_lalu;

            $shuPeriod->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
            ]);
            $shuPeriod->total_shu = $total_shu;
            return $this->sendResponse($shuPeriod, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
