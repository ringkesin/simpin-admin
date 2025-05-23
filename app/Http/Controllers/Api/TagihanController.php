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
        $tokenAbilities = $user->currentAccessToken()->abilities;

        try {
            if (in_array('state:admin', $tokenAbilities)) {
                $tagihanSum = TagihanModels::when($request->p_anggota_id, function ($query, $p_anggota_id) {
                                            $query->where('p_anggota_id', $p_anggota_id);
                                        })
                                        ->when($request->bulan, function ($query, $bulan) {
                                            $query->where('bulan', $bulan);
                                        })
                                        ->when($request->tahun, function ($query, $tahun) {
                                            $query->where('tahun', $tahun);
                                        })
                                        ->whereNull('deleted_at')
                                        ->sum('jumlah_tagihan');

                $tagihanPeriod = TagihanModels::with('pinjamanAnggota')
                                            ->with('statusPembayaran')
                                            ->with('metodePembayaran')
                                            ->when($request->p_anggota_id, function ($query, $p_anggota_id) {
                                                $query->where('p_anggota_id', $p_anggota_id);
                                            })
                                            ->when($request->bulan, function ($query, $bulan) {
                                                $query->where('bulan', $bulan);
                                            })
                                            ->when($request->tahun, function ($query, $tahun) {
                                                $query->where('tahun', $tahun);
                                            })
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
            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::where('user_id', $user->id)->first();
                $tagihanSum = TagihanModels::where('p_anggota_id', $anggota->p_anggota_id)
                                         ->when($request->bulan, function ($query, $bulan) {
                                            $query->where('bulan', $bulan);
                                        })
                                        ->when($request->tahun, function ($query, $tahun) {
                                            $query->where('tahun', $tahun);
                                        })
                                        ->whereNull('deleted_at')
                                        ->sum('jumlah_tagihan');

                $tagihanPeriod = TagihanModels::with('pinjamanAnggota')
                                            ->with('statusPembayaran')
                                            ->with('metodePembayaran')
                                            ->where('p_anggota_id', $anggota->p_anggota_id)
                                            ->when($request->bulan, function ($query, $bulan) {
                                                $query->where('bulan', $bulan);
                                            })
                                            ->when($request->tahun, function ($query, $tahun) {
                                                $query->where('tahun', $tahun);
                                            })
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
            }
            return $this->sendResponse(['total_tagihan' => $tagihanSum ,'tagihan' => $tagihanPeriod], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getByNomorPinjaman(Request $request)
    {
        $user = Auth::user();
        $tokenAbilities = $user->currentAccessToken()->abilities;

        try {
            if (in_array('state:admin', $tokenAbilities)) {
                $tagihanSum = TagihanModels::whereHas('pinjamanAnggota', function ($query) use ($request) {
                                                $query->where('nomor_pinjaman', $request->nomor_pinjaman);
                                            })
                                            ->when($request->bulan, function ($query, $bulan) {
                                                $query->where('bulan', $bulan);
                                            })
                                            ->when($request->tahun, function ($query, $tahun) {
                                                $query->where('tahun', $tahun);
                                            })
                                            ->whereNull('deleted_at')
                                            ->sum('jumlah_tagihan');

                $tagihanPeriod = TagihanModels::with('pinjamanAnggota')
                                            ->with('statusPembayaran')
                                            ->with('metodePembayaran')
                                            ->whereHas('pinjamanAnggota', function ($query) use ($request) {
                                                $query->where('nomor_pinjaman', $request->nomor_pinjaman);
                                            })
                                            ->when($request->bulan, function ($query, $bulan) {
                                                $query->where('bulan', $bulan);
                                            })
                                            ->when($request->tahun, function ($query, $tahun) {
                                                $query->where('tahun', $tahun);
                                            })
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
            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $anggota = AnggotaModels::where('user_id', $user->id)->first();
                $tagihanSum = TagihanModels::where('p_anggota_id', $anggota->p_anggota_id)
                                        ->whereHas('pinjamanAnggota', function ($query) use ($request) {
                                            $query->where('nomor_pinjaman', $request->nomor_pinjaman);
                                        })
                                        ->when($request->bulan, function ($query, $bulan) {
                                            $query->where('bulan', $bulan);
                                        })
                                        ->when($request->tahun, function ($query, $tahun) {
                                            $query->where('tahun', $tahun);
                                        })
                                        ->whereNull('deleted_at')
                                        ->sum('jumlah_tagihan');

                $tagihanPeriod = TagihanModels::with('pinjamanAnggota')
                                            ->with('statusPembayaran')
                                            ->with('metodePembayaran')
                                            ->where('p_anggota_id', $anggota->p_anggota_id)
                                            ->whereHas('pinjamanAnggota', function ($query) use ($request) {
                                                $query->where('nomor_pinjaman', $request->nomor_pinjaman);
                                            })
                                            ->when($request->bulan, function ($query, $bulan) {
                                                $query->where('bulan', $bulan);
                                            })
                                            ->when($request->tahun, function ($query, $tahun) {
                                                $query->where('tahun', $tahun);
                                            })
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
            }

            return $this->sendResponse(['total_tagihan' => $tagihanSum ,'tagihan' => $tagihanPeriod], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
