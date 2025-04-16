<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Main\PinjamanModels;
use App\Models\Master\PinjamanKeperluanModels;
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
                'doc_ktp' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'doc_ktp_suami_istri' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'doc_kk' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'doc_kartu_anggota' => 'required|file|mimes:jpg,png,pdf|max:2048',
                'doc_slip_gaji' => 'required|file|mimes:jpg,png,pdf|max:2048',
            ],[
                'p_anggota_id.required' => 'Anggota harus diisi',
                'p_jenis_pinjaman_id.required' => 'Jenis Pinjaman harus diisi',
                'p_pinjaman_keperluan_ids.required' => 'Keperluan Pinjaman harus diisi',
                'jenis_barang.required' => 'Jenis Barang harus diisi',
                'merk_type.required' => 'Merk / Tipe Barang harus diisi',
                'tenor.required' => 'Tenor harus diisi',
                'ra_jumlah_pinjaman.required' => 'Jumlah Pengajuan Pinjaman harus diisi',
                'jaminan.required' => 'Jaminan harus diisi',
                'jaminan_perkiraan_nilai.required' => 'Perkiraan Nilai Jaminan harus diisi',
                'no_rekening.required' => 'Nomor Rekening harus diisi',
                'bank.required' => 'Bank harus diisi',
                'doc_ktp' => 'File KTP Pemohon harus diupload',
                'doc_ktp_suami_istri' => 'File KTP Suami / Istri harus diupload',
                'doc_kk' => 'File KK harus diupload',
                'doc_kartu_anggota' => 'File Kartu Anggota harus diupload',
                'doc_slip_gaji' => 'File Slip Gaji harus diupload',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            $user = $request->user();
            $p_anggota_id = $user->anggota?->p_anggota_id;
            if( ! empty($p_anggota_id)){
                $doc_ktp_path = $request->file('doc_ktp')->store('uploads/ktp', 'local');
                $doc_doc_ktp_suami_istri_path = $request->file('doc_ktp_suami_istri')->store('uploads/ktp_suami_istri', 'local');
                $doc_kk_path = $request->file('doc_kk')->store('uploads/kartu_keluarga', 'local');
                $doc_kartu_anggota_path = $request->file('doc_kartu_anggota')->store('uploads/kartu_anggota', 'local');
                $doc_slip_gaji_path = $request->file('doc_slip_gaji')->store('uploads/slip_gaji', 'local');

                $pinjaman = PinjamanModels::create([
                    'p_anggota_id' => $p_anggota_id,
                    'p_jenis_pinjaman_id' => $request->p_jenis_pinjaman_id,
                    'p_pinjaman_keperluan_ids' => $request->p_pinjaman_keperluan_ids,
                    'jenis_barang' => $request->jenis_barang,
                    'merk_type' => $request->merk_type,
                    'tenor' => $request->tenor,
                    'ra_jumlah_pinjaman' => $request->ra_jumlah_pinjaman,
                    'ri_jumlah_pinjaman' => 0,
                    'jaminan' => $request->jaminan,
                    'jaminan_keterangan' => $request->jaminan_keterangan,
                    'jaminan_perkiraan_nilai' => $request->jaminan_perkiraan_nilai,
                    'no_rekening' => $request->no_rekening,
                    'bank' => $request->bank,
                    'doc_ktp' => $doc_ktp_path,
                    'doc_ktp_suami_istri' => $doc_doc_ktp_suami_istri_path,
                    'doc_kk' => $doc_kk_path,
                    'doc_kartu_anggota' => $doc_kartu_anggota_path,
                    'doc_slip_gaji' => $doc_slip_gaji_path,
                    'p_status_pengajuan_id' => 1,
                    'created_by' => $user->id,
                    'updated_by' => $user->id
                ]);

                DB::commit();

                return $this->sendResponse(['pinjaman' => $pinjaman], 'Pengajuan Pinjaman Berhasil Disubmit');
            }
            else {
                return $this->sendError('Not Found', ['error' => 'Data anggota tidak ditemukan'], 404);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function listPengajuan(Request $request)
    {
        try {
            $user = $request->user();
            $p_anggota_id = $user->anggota?->p_anggota_id;

            if (!$p_anggota_id) {
                return $this->sendError('Data anggota tidak ditemukan.', [], 404);
            }

            $statusId = $request->input('p_status_pengajuan_id');
            $jenisPinjamanId = $request->input('p_jenis_pinjaman_id');
            $p_pinjaman_keperluan_id = $request->input('p_pinjaman_keperluan_id');
            $month = $request->input('month'); // e.g., 4 (April)
            $year = $request->input('year'); // e.g., 2025

            $query = PinjamanModels::with(['masterJenisPinjaman','masterStatusPengajuan'])->where('p_anggota_id', $p_anggota_id);

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
    
                return $item;
            });

            return $this->sendResponse($pinjaman, 'Daftar Pinjaman Berhasil Diambil');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}