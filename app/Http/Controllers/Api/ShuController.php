<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\DB;
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
        $tokenAbilities = $user->currentAccessToken()->abilities;

        try {
            if (in_array('state:admin', $tokenAbilities)) {
                if(!empty($request->p_anggota_id)) {
                    $shuPeriod = ShuModels::where('p_anggota_id', $request->p_anggota_id)
                                            ->where('tahun', $request->tahun)
                                            ->whereNull('deleted_at')
                                            ->first();
                    $total_shu = 0;

                    if(!empty($shuPeriod)) {
                        $total_shu = $total_shu
                                            + $shuPeriod->shu_diterima
                                            + $shuPeriod->shu_dibagi
                                            + $shuPeriod->shu_ditabung
                                            + $shuPeriod->shu_tahun_lalu;

                        $shuPeriod->total_shu = $total_shu;
                        $shuPeriod->makeHidden([
                            'created_at',
                            'updated_at',
                            'deleted_at',
                            'created_by',
                            'updated_by',
                            'deleted_by',
                        ]);
                    } else {
                        $shuPeriod['p_anggota_id'] = $request->p_anggota_id;
                        $shuPeriod['tahun'] = $request->tahun;
                        $shuPeriod['shu_diterima'] = 0;
                        $shuPeriod['shu_dibagi'] = 0;
                        $shuPeriod['shu_ditabung'] = 0;
                        $shuPeriod['shu_tahun_lalu'] = 0;
                    }
                } else {
                    $shuPeriod = ShuModels::where('tahun', $request->tahun)
                                            ->whereNull('deleted_at');

                    $shu = [
                        'shu_diterima' => $shuPeriod->sum('shu_diterima'),
                        'shu_dibagi' => $shuPeriod->sum('shu_dibagi'),
                        'shu_ditabung' => $shuPeriod->sum('shu_ditabung'),
                        'shu_tahun_lalu' => $shuPeriod->sum('shu_tahun_lalu'),
                    ];

                    $total_shu = 0;

                    if(!empty($shuPeriod)) {
                        $total_shu = $total_shu
                                            + $shu['shu_diterima']
                                            + $shu['shu_dibagi']
                                            + $shu['shu_ditabung']
                                            + $shu['shu_tahun_lalu'];
                    }

                    $shu['tahun'] = $request->tahun;
                    $shu['total_shu'] = $total_shu;
                    $shuPeriod = $shu;
                }

            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::where('user_id', $user->id)->first();

                $shuPeriod = ShuModels::where('p_anggota_id', $anggota->p_anggota_id)
                                            ->where('tahun', $request->tahun)
                                            ->whereNull('deleted_at')
                                            ->first();
                $total_shu = 0;

                if(!empty($shuPeriod)) {
                    $total_shu = $total_shu
                                        + $shuPeriod->shu_diterima
                                        + $shuPeriod->shu_dibagi
                                        + $shuPeriod->shu_ditabung
                                        + $shuPeriod->shu_tahun_lalu;

                    $shuPeriod->total_shu = $total_shu;
                    $shuPeriod->makeHidden([
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        'created_by',
                        'updated_by',
                        'deleted_by',
                    ]);
                } else {
                    $shuPeriod['p_anggota_id'] = $anggota->p_anggota_id;
                    $shuPeriod['tahun'] = $request->tahun;
                    $shuPeriod['shu_diterima'] = 0;
                    $shuPeriod['shu_dibagi'] = 0;
                    $shuPeriod['shu_ditabung'] = 0;
                    $shuPeriod['shu_tahun_lalu'] = 0;
                }
            }
            return $this->sendResponse($shuPeriod, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getByAnggotaGrid(Request $request)
    {
        $user = Auth::user();
        $tokenAbilities = $user->currentAccessToken()->abilities;
        $page = $request->page; // default page 1 jika tidak ada
        $perPage = $request->perpage;
        $offset = ($page - 1) * $perPage;
        $data = $request->data;
        // return $offset;

        try {
            $shuResult = [];
            $total_shu = 0;
            if (in_array('state:admin', $tokenAbilities)) {
                if(!empty($data['p_anggota_id'])) {
                    $result = ShuModels::where('p_anggota_id', $data['p_anggota_id'])
                                            ->whereNull('deleted_at')
                                            ->offset($offset)
                                            ->limit($perPage)
                                            ->get();
                    // return $result;
                    if(!empty($result)) {
                        $totalShu = ShuModels::where('p_anggota_id', $data['p_anggota_id'])
                                            ->whereNull('deleted_at')
                                            ->sum('shu_diterima');
                        $shuResult['total_shu'] = $totalShu;
                        $shuResult['result'] = $result;
                    } else {
                        $shuResult['total_shu'] = $total_shu;
                        $shuResult['result'] = $result;
                    }
                } else {
                    $result = ShuModels::select('tahun', DB::raw('SUM(shu_diterima) as total_shu_diterima'))
                                        ->whereNull('deleted_at')
                                        ->groupBy('tahun')
                                        ->orderByDesc('tahun')
                                        ->offset($offset)
                                        ->limit($perPage)
                                        ->get();

                    if(!empty($result)) {
                        $totalShu = ShuModels::whereNull('deleted_at')
                                                ->sum('shu_diterima');
                        $shuResult['total_shu'] = $totalShu;
                        $shuResult['result'] = $result;
                    } else {
                        $shuResult['total_shu'] = $total_shu;
                        $shuResult['result'] = $result;
                    }
                }

            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::where('user_id', $user->id)->first();

                $result = ShuModels::where('p_anggota_id', $anggota['p_anggota_id'])
                                    ->whereNull('deleted_at')
                                    ->offset($offset)
                                    ->limit($perPage)
                                    ->get();

                // return $result;
                if(!empty($result)) {
                    $totalShu = ShuModels::where('p_anggota_id', $anggota['p_anggota_id'])
                                        ->whereNull('deleted_at')
                                        ->sum('shu_diterima');

                    $shuResult['total_shu'] = $totalShu;
                    $shuResult['result'] = $result;
                } else {
                    $shuResult['total_shu'] = $total_shu;
                    $shuResult['result'] = $result;
                }
            }
            return $this->sendResponse($shuResult, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
