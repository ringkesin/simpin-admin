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
        $tokenAbilities = $user->currentAccessToken()->abilities;

        try {
            if (in_array('state:admin', $tokenAbilities)) {
                if(!empty($request->p_anggota_id)) {
                    $tabunganPeriod = TabunganModels::where('p_anggota_id', $request->p_anggota_id)
                                            ->where('bulan', $request->bulan)
                                            ->where('tahun', $request->tahun)
                                            ->whereNull('deleted_at')
                                            ->first();
                    $total_tabungan = 0;

                    if(!empty($tabunganPeriod)) {
                        $total_tabungan = $total_tabungan
                                            + $tabunganPeriod->simpanan_pokok
                                            + $tabunganPeriod->simpanan_wajib
                                            + $tabunganPeriod->tabungan_sukarela
                                            + $tabunganPeriod->tabungan_indir
                                            + $tabunganPeriod->kompensasi_masa_kerja;
                        $tabunganPeriod->total_tabungan = $total_tabungan;
                        $tabunganPeriod->makeHidden([
                            'created_at',
                            'updated_at',
                            'deleted_at',
                            'created_by',
                            'updated_by',
                            'deleted_by',
                        ]);
                    } else {
                        $tabunganPeriod['p_anggota_id'] = $request->p_anggota_id;
                        $tabunganPeriod['bulan'] = $request->bulan;
                        $tabunganPeriod['tahun'] = $request->tahun;
                        $tabunganPeriod['total_tabungan'] = 0;
                        $tabunganPeriod['simpanan_pokok'] = 0;
                        $tabunganPeriod['simpanan_wajib'] = 0;
                        $tabunganPeriod['tabungan_sukarela'] = 0;
                        $tabunganPeriod['tabungan_indir'] = 0;
                        $tabunganPeriod['kompensasi_masa_kerja'] = 0;
                    }
                } else {
                    $tabunganPeriod = TabunganModels::where('bulan', $request->bulan)
                                            ->where('tahun', $request->tahun)
                                            ->whereNull('deleted_at');

                    $tabungan = [
                        'simpanan_pokok'   => $tabunganPeriod->sum('simpanan_pokok'),
                        'simpanan_wajib'    => $tabunganPeriod->sum('simpanan_wajib'),
                        'tabungan_sukarela' => $tabunganPeriod->sum('tabungan_sukarela'),
                        'tabungan_indir'    => $tabunganPeriod->sum('tabungan_indir'),
                        'kompensasi_masa_kerja' => $tabunganPeriod->sum('kompensasi_masa_kerja')
                    ];

                    $total_tabungan = 0;
                    if(!empty($tabunganPeriod)) {
                        $total_tabungan = $total_tabungan
                                        + $tabungan['simpanan_pokok']
                                        + $tabungan['simpanan_wajib']
                                        + $tabungan['tabungan_sukarela']
                                        + $tabungan['tabungan_indir']
                                        + $tabungan['kompensasi_masa_kerja'];
                    }

                    $tabungan['bulan'] = $request->bulan;
                    $tabungan['tahun'] = $request->tahun;
                    $tabungan['total_tabungan'] = $total_tabungan;
                    $tabunganPeriod = $tabungan;
                }

            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::where('user_id', $user->id)->first();

                $tabunganPeriod = TabunganModels::where('p_anggota_id', $anggota->p_anggota_id)
                                            ->where('bulan', $request->bulan)
                                            ->where('tahun', $request->tahun)
                                            ->whereNull('deleted_at')
                                            ->first();
                $total_tabungan = 0;

                if(!empty($tabunganPeriod)) {
                    $total_tabungan = $total_tabungan
                                        + $tabunganPeriod->simpanan_pokok
                                        + $tabunganPeriod->simpanan_wajib
                                        + $tabunganPeriod->tabungan_sukarela
                                        + $tabunganPeriod->tabungan_indir
                                        + $tabunganPeriod->kompensasi_masa_kerja;

                    $tabunganPeriod->total_tabungan = $total_tabungan;
                    $tabunganPeriod->makeHidden([
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        'created_by',
                        'updated_by',
                        'deleted_by',
                    ]);
                } else {
                    $tabunganPeriod['p_anggota_id'] = $anggota->p_anggota_id;
                    $tabunganPeriod['bulan'] = $request->bulan;
                    $tabunganPeriod['tahun'] = $request->tahun;
                    $tabunganPeriod['total_tabungan'] = 0;
                    $tabunganPeriod['simpanan_pokok'] = 0;
                    $tabunganPeriod['simpanan_wajib'] = 0;
                    $tabunganPeriod['tabungan_sukarela'] = 0;
                    $tabunganPeriod['tabungan_indir'] = 0;
                    $tabunganPeriod['kompensasi_masa_kerja'] = 0;
                }
            }

            return $this->sendResponse($tabunganPeriod, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
