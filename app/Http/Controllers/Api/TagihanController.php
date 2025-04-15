<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Master\AnggotaModels;
use App\Models\Main\TagihanModels;
use Illuminate\Support\Facades\Auth;
use Exception;

class TagihanController extends BaseController
{
    public function getByAnggota(Request $request)
    {
        $user = Auth::user();

        try {
            $anggota = AnggotaModels::where('user_id', $user->id)->first();
            $tagihanSum = TagihanModels::where('p_anggota_id', $anggota->p_anggota_id)
                                    ->where('bulan', $request->bulan)
                                    ->where('tahun', $request->tahun)
                                    ->whereNull('deleted_at')
                                    ->sum('jumlah');

            $tagihanPeriod = TagihanModels::where('p_anggota_id', $anggota->p_anggota_id)
                                        ->where('bulan', $request->bulan)
                                        ->where('tahun', $request->tahun)
                                        ->whereNull('deleted_at')
                                        ->get();

            $tagihanPeriod->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
            ]);
            return $this->sendResponse(['total_tagihan' => $tagihanSum ,'tagihan' => $tagihanPeriod], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
