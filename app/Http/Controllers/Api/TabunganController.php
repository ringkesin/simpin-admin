<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\AnggotaModels;
use App\Models\Main\TabunganModels;
use App\Models\Main\TabunganSaldoModels;
use App\Models\Main\TabunganJurnalModels;
use App\Models\Main\TabunganPengambilanModels;
use App\Models\Main\TabunganPenyertaanModels;
use App\Models\Master\JenisTabunganModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function getMutasi(Request $request)
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
            $isAdmin = $user->tokenCan('state:admin');
            $isAnggota = $user->tokenCan('state:anggota');

            if($isAdmin){
                $query = TabunganJurnalModels::with(['jenisTabungan:p_jenis_tabungan_id,nama','masterAnggota:p_anggota_id,nomor_anggota,nama,nik'])
                    ->whereYear('tgl_transaksi', $request->tahun)
                    ->whereMonth('tgl_transaksi', $request->bulan);
            }
            if($isAnggota) {
                $p_anggota_id = $user->anggota?->p_anggota_id;
                if (!$p_anggota_id) {
                    return $this->sendError('Data anggota tidak ditemukan.', [], 404);
                }
                $query = TabunganJurnalModels::with(['jenisTabungan:p_jenis_tabungan_id,nama','masterAnggota:p_anggota_id,nomor_anggota,nama,nik'])
                    ->where('p_anggota_id', $p_anggota_id)
                    ->whereYear('tgl_transaksi', $request->tahun)
                    ->whereMonth('tgl_transaksi', $request->bulan);
            }

            $listPengajuan = $query
                ->orderBy('tgl_transaksi', 'desc')
                ->paginate(10)
                ->through(fn ($item) => $item->makeHidden([
                    'p_anggota_id','p_jenis_tabungan_id','updated_at','deleted_at', 'created_by', 'updated_by','deleted_by',
                ]));

            return $this->sendResponse($listPengajuan, 'Daftar Mutasi Tabungan Berhasil Diambil');
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

            $get = TabunganSaldoModels::with('jenisTabungan')->where('p_anggota_id', $request->p_anggota_id)->where('tahun', $request->tahun)->get();
            if( ! $get){
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $totalSaldo = 0;
            $detailSaldo = [];
            foreach($get as $g){
                $totalSaldo = $totalSaldo + $g->total_sd;
                $detailSaldo[] = [
                    'p_jenis_tabungan_id' => $g->p_jenis_tabungan_id,
                    'jenis_tabungan' => $g->jenisTabungan->nama,
                    'saldo_sd_bulan_ini' => $g->total_sd
                ];
            }

            $saldo = [
                'tahun' => $request->tahun,
                'total_saldo_sd' => $totalSaldo,
                'detail' => $detailSaldo
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

            $saldo = [];
            $totalBulanIni = 0;
            $totalBulanIniSd = 0;
            $jenisTabungan = JenisTabunganModels::all();
            foreach($jenisTabungan as $j){
                $bulanIni = TabunganJurnalModels::select(DB::raw('SUM(nilai) as total_nilai'))
                    ->where('p_anggota_id', $request->p_anggota_id)
                    ->where('p_jenis_tabungan_id', $j->p_jenis_tabungan_id)
                    ->whereYear('tgl_transaksi', $request->tahun)
                    ->whereMonth('tgl_transaksi', $request->bulan)
                    ->first();
                $bulanIni = $bulanIni->total_nilai ?? 0;

                $sdBulanIni = TabunganJurnalModels::select(DB::raw('SUM(nilai) as total_nilai'))
                    ->where('p_anggota_id', $request->p_anggota_id)
                    ->where('p_jenis_tabungan_id', $j->p_jenis_tabungan_id)
                    ->whereYear('tgl_transaksi', $request->tahun)
                    ->whereMonth('tgl_transaksi', '<=', $request->bulan)
                    ->first();
                $sdBulanIni = $sdBulanIni->total_nilai ?? 0;

                $saldo[] = [
                    'p_jenis_tabungan_id' => $j->p_jenis_tabungan_id,
                    'jenis_tabungan' => $j->nama,
                    'saldo_bulan_ini' => $bulanIni,
                    'saldo_sd_bulan_ini' => $sdBulanIni,
                ];

                $totalBulanIni = $totalBulanIni + $bulanIni;
                $totalBulanIniSd = $totalBulanIniSd + $sdBulanIni;
            }
            return $this->sendResponse([
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'total' => [
                    'saldo_bulan_ini' => $totalBulanIni,
                    'saldo_sd_bulan_ini' => $totalBulanIniSd,
                ],
                'detail' => $saldo
            ], 'Data Berhasil Ditampilkan');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /* Pencairan */
    public function formPengajuanPencairan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
                'p_jenis_tabungan_id' => 'required|integer|exists:p_jenis_tabungan,p_jenis_tabungan_id',
                'jumlah_diambil' => 'required|numeric',
                'rekening_bank' => 'required|string|max:200',
                'rekening_no' => 'required|numeric',
                'keterangan' => 'nullable|max:2024',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'p_jenis_tabungan_id.required' => 'Jenis Tabungan harus diisi',
                'jumlah_diambil.required' => 'Jumlah Diambil harus diisi',
                'rekening_bank.required' => 'Rekening Bank harus diisi',
                'rekening_no.required' => 'Nomor Rekening harus diisi',
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

            DB::beginTransaction();

            $pengajuan_pencairan = TabunganPengambilanModels::create([
                'p_anggota_id' => $request->p_anggota_id,
                'p_jenis_tabungan_id' => $request->p_jenis_tabungan_id,
                'tgl_pengajuan' => date('Y-m-d H:i:s'),
                'jumlah_diambil' => $request->jumlah_diambil,
                'rekening_no' => $request->rekening_no,
                'rekening_bank' => $request->rekening_bank,
                'status_pengambilan' => 'PENDING', //PENDING, DIVERIFIKASI, DISETUJUI, DITOLAK, DIBATALKAN_ANGGOTA
                'catatan_user' => $request->keterangan,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return $this->sendResponse(['pengajuan_pencairan' => $pengajuan_pencairan], 'Pengajuan Pencairan Tabungan Berhasil Disubmit');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function listPengajuanPencairan(Request $request)
    {
        try {
            $user = $request->user();
            $isAdmin = $user->tokenCan('state:admin');
            $isAnggota = $user->tokenCan('state:anggota');

            if($isAdmin){
                $query = TabunganPengambilanModels::with(['jenisTabungan:p_jenis_tabungan_id,nama','masterAnggota:p_anggota_id,nomor_anggota,nama,nik']);
            }
            if($isAnggota) {
                $p_anggota_id = $user->anggota?->p_anggota_id;
                if (!$p_anggota_id) {
                    return $this->sendError('Data anggota tidak ditemukan.', [], 404);
                }
                $query = TabunganPengambilanModels::with(['jenisTabungan:p_jenis_tabungan_id,nama','masterAnggota:p_anggota_id,nomor_anggota,nama,nik'])
                    ->where('p_anggota_id', $p_anggota_id);
            }

            $listPengajuan = $query
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->through(fn ($item) => $item->makeHidden([
                    'p_anggota_id','p_jenis_tabungan_id','deleted_at', 'created_by', 'updated_by','deleted_by',
                ]));

            return $this->sendResponse($listPengajuan, 'Daftar Pengajuan Pencairan Tabungan Berhasil Diambil');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function batalkanPencairan(Request $request, $id)
    {
        $user = $request->user();
        $isAnggota = $user->tokenCan('state:anggota');
        $p_anggota_id = $user->anggota?->p_anggota_id;

        $data = TabunganPengambilanModels::find($id);
        if (! $data) {
            return response()->json(['message' => 'Data pengajuan pencairan tabungan tidak ditemukan.'], 404);
        }

        if ($isAnggota && $data->p_anggota_id !== $p_anggota_id) {
            return response()->json(['message' => 'Tidak diizinkan menghapus pencairan ini.'], 403);
        }

        if ($isAnggota){
            if($data->status_pengambilan !== 'PENDING') //available status = 'PENDING, DIVERIFIKASI, DISETUJUI, DITOLAK'
            {
                return response()->json(['message' => "Tidak diizinkan menghapus pengajuan ini, karena statusnya tidak lagi 'Pending'."], 403);
            }
        }

        $user = $request->user();
        $data->deleted_by = $user->id;
        $data->save();
        $data->delete();

        return $this->sendResponse([], 'Data pengajuan pencairan tabungan berhasil dihapus');
    }

    public function approvalPencairan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_pencairan' => 'required',
            'tgl_pencairan' => 'nullable|date|date_format:Y-m-d',
            'jumlah_disetujui' => 'required|numeric',
            'catatan_approver' =>  'required|max:2024',
        ],[
            'status_pencairan.required' => 'Status Pencairan harus diisi.',
            'tgl_pencairan.required' => 'Tgl. Pencairan harus diisi.',
            'jumlah_disetujui.required' => 'Jumlah harus diisi.',
            'catatan_approver.required' => 'Catatan harus diisi.',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $data = TabunganPengambilanModels::findOrFail($request->id);

            TabunganPengambilanModels::where('t_tabungan_pengambilan_id', $request->id)->update([
                'status_pengambilan' => $request->status_pencairan,
                'tgl_pencairan' => ( ! empty($request->tgl_pencairan)) ? $request->tgl_pencairan.' '.date('H:i:s') : null,
                'jumlah_disetujui' => $request->jumlah_disetujui,
                'catatan_approver' => $request->catatan_approver
            ]);

            if($request->status_pencairan == 'DISETUJUI') {
                TabunganJurnalModels::create([
                    'p_anggota_id' => $data->p_anggota_id,
                    'p_jenis_tabungan_id' => $data->p_jenis_tabungan_id,
                    'tgl_transaksi' => date('Y-m-d H:i:s'),
                    'nilai' => '-'.$request->jumlah_disetujui,
                    'nilai_sd' => 0,
                    'catatan' => 'Pencairan Tabungan : '.$request->catatan_approver
                ]);

                DB::select('SELECT _tabungan_recalculate(:p_anggota_id, :tahun)', [
                    'p_anggota_id' => $data->p_anggota_id,
                    'tahun' => date('Y'),
                ]);
            }

            DB::commit();
            return $this->sendResponse(['t_tabungan_pengambilan_id' => $request->id], 'Pengajuan Pencairan Tabungan Berhasil Disubmit');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    /* Penyertaan */
    public function formPengajuanPenyertaan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
                'p_jenis_tabungan_id' => 'required|integer|exists:p_jenis_tabungan,p_jenis_tabungan_id',
                'jumlah' => 'required|numeric',
                'tanggal_penyertaan' => 'required|date',
                'keterangan' => 'nullable|max:2024',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'p_jenis_tabungan_id.required' => 'Jenis Tabungan harus diisi',
                'jumlah.required' => 'Jumlah harus diisi',
                'tanggal_penyertaan.required' => 'Tanggal Penyertaan harus diisi',
                'tanggal_penyertaan.date' => 'Tanggal Penyertaan harus format tanggal',
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

            DB::beginTransaction();

            $pengajuan_penyertaan = TabunganPenyertaanModels::create([
                'p_anggota_id' => $request->p_anggota_id,
                'p_jenis_tabungan_id' => $request->p_jenis_tabungan_id,
                'penyertaan_date' => $request->tanggal_penyertaan,
                'jumlah' => $request->jumlah,
                'status_penyertaan' => 'PENDING', //PENDING, DIVERIFIKASI, DISETUJUI, DITOLAK, DIBATALKAN_ANGGOTA
                'catatan_user' => $request->keterangan,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return $this->sendResponse(['pengajuan_penyertaan' => $pengajuan_penyertaan], 'Pengajuan Penyertaan Tabungan Berhasil Disubmit');
        } catch (Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function listPengajuanPenyertaan(Request $request)
    {
        try {
            $user = $request->user();
            $isAdmin = $user->tokenCan('state:admin');
            $isAnggota = $user->tokenCan('state:anggota');

            if($isAdmin){
                $query = TabunganPenyertaanModels::with(['jenisTabungan:p_jenis_tabungan_id,nama','masterAnggota:p_anggota_id,nomor_anggota,nama,nik']);
            }
            if($isAnggota) {
                $p_anggota_id = $user->anggota?->p_anggota_id;
                if (!$p_anggota_id) {
                    return $this->sendError('Data anggota tidak ditemukan.', [], 404);
                }
                $query = TabunganPenyertaanModels::with(['jenisTabungan:p_jenis_tabungan_id,nama','masterAnggota:p_anggota_id,nomor_anggota,nama,nik'])
                    ->where('p_anggota_id', $p_anggota_id);
            }

            $listPengajuan = $query
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->through(fn ($item) => $item->makeHidden([
                    'p_anggota_id','p_jenis_tabungan_id','deleted_at', 'created_by', 'updated_by','deleted_by',
                ]));

            return $this->sendResponse($listPengajuan, 'Daftar Pengajuan Penyertaan Tabungan Berhasil Diambil');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function batalkanPenyertaan(Request $request, $id)
    {
        $user = $request->user();
        $isAnggota = $user->tokenCan('state:anggota');
        $p_anggota_id = $user->anggota?->p_anggota_id;

        $data = TabunganPenyertaanModels::find($id);
        if (! $data) {
            return response()->json(['message' => 'Data pengajuan pencairan tabungan tidak ditemukan.'], 404);
        }

        if ($isAnggota && $data->p_anggota_id !== $p_anggota_id) {
            return response()->json(['message' => 'Tidak diizinkan menghapus pencairan ini.'], 403);
        }

        if ($isAnggota){
            if($data->status_pengambilan !== 'PENDING') //available status = 'PENDING, DIVERIFIKASI, DISETUJUI, DITOLAK'
            {
                return response()->json(['message' => "Tidak diizinkan menghapus pengajuan ini, karena statusnya tidak lagi 'Pending'."], 403);
            }
        }

        $user = $request->user();
        $data->deleted_by = $user->id;
        $data->save();
        $data->delete();

        return $this->sendResponse([], 'Data pengajuan pencairan tabungan berhasil dihapus');
    }

    public function approvalPenyertaan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_penyertaan' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal_penyertaan' => 'required|date',
            'catatan_approver' =>  'required|max:2024',
        ],[
            'status_penyertaan.required' => 'Status Penyertaan harus diisi.',
            'jumlah.required' => 'Jumlah harus diisi',
            'tanggal_penyertaan.required' => 'Tanggal Penyertaan harus diisi',
            'tanggal_penyertaan.date' => 'Tanggal Penyertaan harus format tanggal',
            'catatan_approver.required' => 'Catatan harus diisi.',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
        }

        try {
            DB::beginTransaction();

            $data = TabunganPenyertaanModels::findOrFail($request->id);

            TabunganPenyertaanModels::where('t_tabungan_penyertaan_id', $request->id)->update([
                'status_penyertaan' => $request->status_penyertaan,
                'penyertaan_date' => ( ! empty($request->tanggal_penyertaan)) ? $request->tanggal_penyertaan.' '.date('H:i:s') : null,
                'jumlah' => $request->jumlah,
                'catatan_approver' => $request->catatan_approver
            ]);

            if($request->status_pencairan == 'DISETUJUI') {
                TabunganJurnalModels::create([
                    'p_anggota_id' => $data->p_anggota_id,
                    'p_jenis_tabungan_id' => $data->p_jenis_tabungan_id,
                    'tgl_transaksi' => date('Y-m-d H:i:s'),
                    'nilai' => $request->jumlah,
                    'nilai_sd' => 0,
                    'catatan' => 'Penyertaan Tabungan : '.$request->catatan_approver
                ]);

                DB::select('SELECT _tabungan_recalculate(:p_anggota_id, :tahun)', [
                    'p_anggota_id' => $data->p_anggota_id,
                    'tahun' => date('Y'),
                ]);
            }

            DB::commit();
            return $this->sendResponse(['t_tabungan_pengambilan_id' => $request->id], 'Pengajuan Pencairan Tabungan Berhasil Disubmit');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
