<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Main\PinjamanModels;
use App\Models\Master\PinjamanKeperluanModels;
use App\Models\Master\JenisPinjamanModels;
use Exception;

class PinjamanController extends BaseController
{
    public function formPengajuan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'p_anggota_id' => 'required|integer|exists:p_anggota,p_anggota_id',
                'p_jenis_pinjaman_id' => 'required|integer|exists:p_jenis_pinjaman,p_jenis_pinjaman_id',
                'p_pinjaman_keperluan_ids' => 'required_unless:p_jenis_pinjaman_id,3|array|nullable',
                'p_pinjaman_keperluan_ids.*' => 'exists:p_pinjaman_keperluan,p_pinjaman_keperluan_id|distinct',
                'jenis_barang' => 'required_if:p_jenis_pinjaman_id,3|nullable|string|max:255',
                'merk_type' => 'required_if:p_jenis_pinjaman_id,3|nullable|string|max:255',
                'tenor' => 'required|integer|min:1',
                'ra_jumlah_pinjaman' => 'required|numeric',
                'jaminan' => 'required|string|max:255',
                'jaminan_keterangan' => 'nullable|string|max:255',
                'jaminan_perkiraan_nilai' => 'required|numeric',
                'no_rekening' => 'required|numeric',
                'bank' => 'required|string|max:255',
                'biaya_admin' => 'required|numeric',
                // 'doc_ktp' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
                // 'doc_ktp_suami_istri' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
                // 'doc_kk' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
                // 'doc_kartu_anggota' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
                'doc_slip_gaji' => 'required|file|mimes:jpg,png,pdf|max:2048',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'p_jenis_pinjaman_id.required' => 'Jenis Pinjaman harus diisi',
                'p_pinjaman_keperluan_ids.required' => 'Keperluan Pinjaman harus diisi',
                'jenis_barang.required' => 'Jenis Barang harus diisi',
                'merk_type.required' => 'Merk / Tipe Barang harus diisi',
                'tenor.required' => 'Tenor harus diisi',
                'biaya_admin.required' => 'Biaya Admin harus diisi',
                'ra_jumlah_pinjaman.required' => 'Jumlah Pengajuan Pinjaman harus diisi',
                'jaminan.required' => 'Jaminan harus diisi',
                'jaminan_perkiraan_nilai.required' => 'Perkiraan Nilai Jaminan harus diisi',
                'no_rekening.required' => 'Nomor Rekening harus diisi',
                'bank.required' => 'Bank harus diisi',
                // 'doc_ktp' => 'File KTP Pemohon harus diupload',
                // 'doc_ktp_suami_istri' => 'File KTP Suami / Istri harus diupload',
                // 'doc_kk' => 'File KK harus diupload',
                // 'doc_kartu_anggota' => 'File Kartu Anggota harus diupload',
                'doc_slip_gaji' => 'File Slip Gaji harus diupload',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            $user = $request->user();
            $isAnggota = $user->tokenCan('state:anggota');
            $p_anggota_id = $user->anggota?->p_anggota_id;

            if ($isAnggota && (int) $request->p_anggota_id !== $p_anggota_id) {
                return response()->json(['message' => 'Tidak diizinkan input data dengan anggota id = '.$request->p_anggota_id], 403);
            }

            //cek kk, buku nikah

            // $doc_ktp_path = $request->file('doc_ktp') ? $request->file('doc_ktp')->store('uploads/ktp', 'local') : NULL;
            // $doc_doc_ktp_suami_istri_path = $request->file('doc_doc_ktp_suami_istri_path') ? $request->file('doc_ktp_suami_istri')->store('uploads/ktp_suami_istri', 'local') : NULL;
            // $doc_kk_path = $request->file('doc_kk') ? $request->file('doc_kk')->store('uploads/kartu_keluarga', 'local') : NULL;
            // $doc_kartu_anggota_path = $request->file('doc_kartu_anggota') ? $request->file('doc_kartu_anggota')->store('uploads/kartu_anggota', 'local') : NULL;
            $doc_slip_gaji_path = $request->file('doc_slip_gaji')->store('uploads/slip_gaji', 'kkba_simpin');

            $pinjaman = PinjamanModels::create([
                'nomor_pinjaman' => $this->generateNomorTransaksi($request->p_jenis_pinjaman_id),
                'p_anggota_id' => $request->p_anggota_id,
                'p_jenis_pinjaman_id' => $request->p_jenis_pinjaman_id,
                'p_pinjaman_keperluan_ids' => ($request->p_jenis_pinjaman_id == 3) ? [] : $request->p_pinjaman_keperluan_ids,
                'jenis_barang' => ($request->p_jenis_pinjaman_id == 3) ? $request->jenis_barang : null,
                'merk_type' => ($request->p_jenis_pinjaman_id == 3) ? $request->merk_type : null,
                'tenor' => $request->tenor,
                'biaya_admin' => $request->biaya_admin,
                'ra_jumlah_pinjaman' => $request->ra_jumlah_pinjaman,
                'ri_jumlah_pinjaman' => 0,
                'jaminan' => $request->jaminan,
                'jaminan_keterangan' => $request->jaminan_keterangan,
                'jaminan_perkiraan_nilai' => $request->jaminan_perkiraan_nilai,
                'no_rekening' => $request->no_rekening,
                'bank' => $request->bank,
                // 'doc_ktp' => $doc_ktp_path,
                // 'doc_ktp_suami_istri' => $doc_doc_ktp_suami_istri_path,
                // 'doc_kk' => $doc_kk_path,
                // 'doc_kartu_anggota' => $doc_kartu_anggota_path,
                'doc_slip_gaji' => $doc_slip_gaji_path,
                'p_status_pengajuan_id' => 2, //pending
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            DB::commit();

            return $this->sendResponse(['pinjaman' => $pinjaman], 'Pengajuan Pinjaman Berhasil Disubmit');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error : ' . $e->getMessage());
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function listPengajuan(Request $request)
    {
        try {
            $user = $request->user();
            $isAdmin = $user->tokenCan('state:admin');
            $isAnggota = $user->tokenCan('state:anggota');

            $statusId = $request->input('p_status_pengajuan_id');
            $jenisPinjamanId = $request->input('p_jenis_pinjaman_id');
            $p_pinjaman_keperluan_id = $request->input('p_pinjaman_keperluan_id');
            $month = $request->input('month'); // e.g., 4 (April)
            $year = $request->input('year'); // e.g., 2025

            if($isAdmin){
                $query = PinjamanModels::with(['masterJenisPinjaman','masterStatusPengajuan','masterAnggota']);
            }
            if($isAnggota) {
                $p_anggota_id = $user->anggota?->p_anggota_id;
                if (!$p_anggota_id) {
                    return $this->sendError('Data anggota tidak ditemukan.', [], 404);
                }
                $query = PinjamanModels::with(['masterJenisPinjaman','masterStatusPengajuan','masterAnggota'])->where('p_anggota_id', $p_anggota_id);
            }

            if ($statusId) {
                $query->where('p_status_pengajuan_id', $statusId);
            }

            if ($jenisPinjamanId) {
                $query->where('p_jenis_pinjaman_id', $jenisPinjamanId);
            }

            if ($p_pinjaman_keperluan_id) {
                $query->whereJsonContains('p_pinjaman_keperluan_ids', $p_pinjaman_keperluan_id);
            }

            if ($month && $year) {
                $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            } elseif ($month) {
                $query->whereMonth('created_at', $month);
            } elseif ($year) {
                $query->whereYear('created_at', $year);
            }

            $pinjaman = $query
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $pinjaman->getCollection()->transform(function ($item) {
                $keperluanIds = is_array($item->p_pinjaman_keperluan_ids)
                    ? $item->p_pinjaman_keperluan_ids
                    : json_decode($item->p_pinjaman_keperluan_ids, true);

                $item->pinjaman_keperluan_nama = PinjamanKeperluanModels::whereIn('p_pinjaman_keperluan_id', $keperluanIds)
                    ->pluck('keperluan');
                // $pinjamans = $item->ri_jumlah_pinjaman ?? 0;
                // $margin = $item->margin ?? 0;

                // $item->estimasi_cicilan_bulanan = round($pinjamans + ($pinjamans * ($margin / 100)));

                $pinjamans = $item->ri_jumlah_pinjaman ?? 0;
                $margin = $item->margin ?? 0;
                $biaya_admin = $item->biaya_admin ?? 0;
                $tenor = $item->tenor ?? 0;

                $item->estimasi_cicilan_bulanan = round($this->calculateInstallments($pinjamans, $margin, $biaya_admin, $tenor));

                return $item;
            });

            return $this->sendResponse($pinjaman, 'Daftar Pinjaman Berhasil Diambil');
        } catch (\Exception $e) {
            \Log::error('Error retrieving pinjaman: ' . $e->getMessage());
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getPengajuanById(Request $request, $id)
    {
        try {
            $data = false;

            //------------Filter by Owner-------------------------------------//
            $user = $request->user();
            $isAdmin = $user->tokenCan('state:admin');
            $isAnggota = $user->tokenCan('state:anggota');

            if ($isAdmin) {
                $data = PinjamanModels::with(['masterJenisPinjaman', 'masterStatusPengajuan', 'masterAnggota'])->find($id);
            }
            elseif ($isAnggota) {
                $p_anggota_id = $user->anggota?->p_anggota_id;
                if (!$p_anggota_id) {
                    return $this->sendError('Data anggota tidak ditemukan.', [], 404);
                }
                $data = PinjamanModels::with(['masterJenisPinjaman', 'masterStatusPengajuan', 'masterAnggota'])
                    ->where('t_pinjaman_id', $id)
                    ->where('p_anggota_id', $p_anggota_id)
                    ->first();
            } else {
                return $this->sendError('Anda tidak memiliki akses.', [], 403);
            }
            //------------End Filter by Owner-------------------------------------//

            if (!$data) {
                return $this->sendError('Pinjaman tidak ditemukan atau tidak memiliki akses.', [], 404);
            }

            $keperluanIds = is_array($data->p_pinjaman_keperluan_ids)
                ? $data->p_pinjaman_keperluan_ids
                : json_decode($data->p_pinjaman_keperluan_ids, true);

            $data->pinjaman_keperluan_nama = PinjamanKeperluanModels::whereIn('p_pinjaman_keperluan_id', $keperluanIds)
                ->pluck('keperluan');

            $pinjaman = $data->ri_jumlah_pinjaman ?? 0;
            $margin = $data->margin ?? 0;
            $biaya_admin = $data->biaya_admin ?? 0;
            $tenor = $data->tenor ?? 0;

            // $data->estimasi_cicilan_bulanan = round($pinjaman + ($pinjaman * ($margin / 100)));
            $data->estimasi_cicilan_bulanan = round($this->calculateInstallments($pinjaman, $margin, $biaya_admin, $tenor));

            return $this->sendResponse($data, 'Data pinjaman berhasil digenerate');
        } catch (\Exception $e) {
            \Log::error('Error retrieving pinjaman: ' . $e->getMessage());
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function deletePengajuanById(Request $request, $id)
    {
        $user = $request->user();
        $isAnggota = $user->tokenCan('state:anggota');
        $p_anggota_id = $user->anggota?->p_anggota_id;

        $pinjaman = PinjamanModels::find($id);
        if (! $pinjaman) {
            return response()->json(['message' => 'Data pinjaman tidak ditemukan.'], 404);
        }

        if ($isAnggota && $pinjaman->p_anggota_id !== $p_anggota_id) {
            return response()->json(['message' => 'Tidak diizinkan menghapus pinjaman ini.'], 403);
        }

        if ($isAnggota){
            if($pinjaman->p_status_pengajuan_id <> 2) //pending
            {
                return response()->json(['message' => "Tidak diizinkan menghapus pinjaman ini, karena statusnya tidak lagi 'Pending'."], 403);
            }
        }

        $pinjaman->delete();

        return $this->sendResponse([], 'Data pinjaman berhasil dihapus');
    }

    function generateNomorTransaksi($jenisPinjamanId)
    {
        $kode = 'PJ';
        $bulan = date('n');
        $tahun = date('Y');

        // Konversi bulan ke romawi
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        // Ambil nomor terakhir dari bulan dan tahun ini
        $last = PinjamanModels::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last) {
            // Ambil nomor urut dari string, misalnya "002/PJ/V/2025" → 2
            $lastNomor = (int)substr($last->nomor_pinjaman, 0, 3);
            $nextNomor = str_pad($lastNomor + 1, 3, '0', STR_PAD_LEFT);
            $inisialJenisPinjaman = $last->masterJenisPinjaman->kode_jenis_pinjaman;
        } else {
            $nextNomor = '001';
            $jenisPinjamanIds = JenisPinjamanModels::find($jenisPinjamanId);
            $inisialJenisPinjaman = $jenisPinjamanIds['kode_jenis_pinjaman'];
        }

        // Gabungkan format akhir
        $nomorBaru = $nextNomor . '/' . $kode . '/'. $inisialJenisPinjaman . '/' . $romawi[$bulan] . '/' . $tahun;
        return $nomorBaru;
    }

    public function calculateInstallments($ri_pinjaman, $margin, $biaya_admin, $tenor) {
        $ri_pinjaman = $ri_pinjaman ?? 0;
        $margin = $margin ?? 0;
        $biaya_admin = $biaya_admin ?? 0;

        $calTenor = $ri_pinjaman / $tenor;
        $calMargin = $ri_pinjaman * ($margin / 100) / $tenor;
        $calAdmin = $ri_pinjaman * ($biaya_admin / 100) / $tenor;

        return $calTenor + $calMargin + $calAdmin;
    }

    public function approvalPinjaman(Request $request) {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                't_pinjaman_id' => 'required',
                'ri_jumlah_pinjaman' => 'required',
                'p_status_pengajuan_id' => 'required',
                'biaya_admin' => 'required|numeric',
                'margin' => 'required|numeric',
                'tenor' => 'required|numeric'

            ],[
                't_pinjaman_id' => 'Pinjaman ID required',
                'ri_jumlah_pinjaman' => 'Jumlah Pinjaman yang Disetujui required',
                'p_status_pengajuan_id.required' => 'Status Pengajuan required.',
                'biaya_admin.required' => 'Biaya Admin required',
                'margin.required' => 'Margin required',
                'tenor.required' => 'Tenor required'
            ]);

            $user = $request->user();

            $post = PinjamanModels::where('t_pinjaman_id', $request->t_pinjaman_id)->update([
                'ri_jumlah_pinjaman' => $request->ri_jumlah_pinjaman,
                'p_status_pengajuan_id' => $request->p_status_pengajuan_id,
                'biaya_admin' => $request->biaya_admin,
                'margin' => $request->margin,
                'tenor' => $request->tenor,
                'remarks' => $request->remarks,
                'tgl_pencairan' => $request->tgl_pencairan ? $request->tgl_pencairan : NULL,
                'tgl_pelunasan' => $request->tgl_pelunasan ? $request->tgl_pelunasan : NULL,
                'updated_by' => $user->id
            ]);

            DB::commit();

            if($post) {
                return $this->sendResponse([], 'Approval Pinjaman Berhasil Disubmit');
            } else {
                return response()->json(['message' => 'Approval Pinjaman Gagal Disubmit.'], 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error : ' . $e->getMessage());
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

}
