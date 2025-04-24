<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\AnggotaModels;
use App\Models\Main\TabunganModels;
use App\Models\Main\TabunganSaldoModels;
use App\Models\Main\TabunganJurnalModels;
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

    public function getSaldoTahunan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
                'tahun' => 'required|integer',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'tahun.required' => 'Tahun harus diisi',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            $user = $request->user();
            $isAnggota = $user->tokenCan('state:anggota');
            $p_anggota_id = $user->anggota?->p_anggota_id;
            
            if ($isAnggota && (int) $request->p_anggota_id !== $p_anggota_id) {
                return response()->json(['message' => 'Tidak diizinkan melihat data dengan anggota id = '.$request->p_anggota_id], 403);
            }

            $get = TabunganSaldoModels::where('p_anggota_id', $request->p_anggota_id)->where('tahun', $request->tahun)->first();
            if( ! $get){
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $saldo = [
                'tahun' => $get->tahun,
                'total_saldo_sd' => $get->total_sd,
                'detail' => [
                    'saldo_sd_simpanan_pokok' => $get->simpanan_pokok,
                    'saldo_sd_simpanan_wajib' => $get->simpanan_wajib,
                    'saldo_sd_tabungan_sukarela' => $get->tabungan_sukarela,
                    'saldo_sd_tabungan_indir' => $get->tabungan_indir,
                    'saldo_sd_kompensasi_masa_kerja' => $get->kompensasi_masa_kerja,
                ]
            ];

            return $this->sendResponse($saldo, 'Data Berhasil Ditampilkan');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getSaldoBulanan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
                'tahun' => 'required|integer',
                'bulan' => 'required|integer',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'tahun.required' => 'Tahun harus diisi',
                'bulan.required' => 'Bulan harus diisi',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            $user = $request->user();
            $isAnggota = $user->tokenCan('state:anggota');
            $p_anggota_id = $user->anggota?->p_anggota_id;
            
            if ($isAnggota && (int) $request->p_anggota_id !== $p_anggota_id) {
                return response()->json(['message' => 'Tidak diizinkan melihat data dengan anggota id = '.$request->p_anggota_id], 403);
            }

            $get = TabunganJurnalModels::with('jenisTabungan')->where('p_anggota_id', $request->p_anggota_id)
                ->where('tahun', $request->tahun)
                ->where('bulan', $request->bulan)
                ->get();
            if( ! $get){
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $saldo = [];
            $totalBulanIni = 0;
            $totalBulanIniSd = 0;
            foreach($get as $g){
                $saldo[] = [
                    'jenis_tabungan' => $g->jenisTabungan->nama,
                    'nilai_bulan_ini' => $g->nilai,
                    'nilai_bulan_ini_sd' => $g->nilai_sd
                ];
                $totalBulanIni = $totalBulanIni + $g->nilai;
                $totalBulanIniSd = $totalBulanIniSd + $g->nilai_sd;
            }

            return $this->sendResponse([
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'total' => [
                    'total_bulan_ini' => $totalBulanIni,
                    'total_bulan_ini_sd' => $totalBulanIniSd,
                ],
                'detail' => $saldo
            ], 'Data Berhasil Ditampilkan');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
