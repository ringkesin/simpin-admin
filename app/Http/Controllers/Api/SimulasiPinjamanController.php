<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\SimulasiPinjamanModels;
use Illuminate\Support\Facades\Auth;
use Exception;

class SimulasiPinjamanController extends BaseController
{
    public function getTenorSimulasi(Request $request)
    {
        try {
            $simulasi = SimulasiPinjamanModels::where('tahun_margin', $request->tahun)
                                            ->where('p_jenis_pinjaman_id', $request->jenis_pinjaman_id)
                                            ->where('status', 'aktif')
                                            ->orderBy('tenor', 'asc')
                                            ->get();
            if(!empty($simulasi)) {
                $result = [];
                foreach($simulasi as $row) {
                    $result[] = [
                        'tenor' => $row['tenor']
                    ];
                }
            } else {
                return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => 'Data Not Found'], 404);
            }

            return $this->sendResponse($result, 'Data berhasil digenerate.');

        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getSimulasi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'jenis_pinjaman_id' => 'required',
                'jumlah_pinjaman' => 'required|integer',
                'tenor' => 'required|integer',
                'tahun' => 'required|integer'
            ],[
                'jenis_pinjaman_id.required' => 'Jenis Pinjaman harus diisi',
                'jumlah_pinjaman.required' => 'Jumlah Pinjaman harus diisi',
                'jumlah_pinjaman.integer' => 'Jumlah Pinjaman harus berupa angka',
                'tenor.required' => 'Tenor harus berupa diisi',
                'tenor.integer' => 'Tenor harus berupa angka',
                'tahun.required' => 'Tahun harus berupa diisi',
                'tahun.integer' => 'Tahun harus berupa angka',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Form belum lengkap, mohon dicek kembali.', ['error' => $validator->errors()], 400);
            }
            $simulasi = SimulasiPinjamanModels::where('tenor', $request->tenor)
                                            ->where('p_jenis_pinjaman_id', $request->jenis_pinjaman_id)
                                            ->where('tahun_margin', $request->tahun)
                                            ->where('status', 'aktif')
                                            ->first();

            $jumlah_pinjaman = $request->jumlah_pinjaman;
            $biaya_admin_rp = (float)($jumlah_pinjaman * ($simulasi->biaya_admin / 100));

            if(!empty($simulasi)) {
                $margin = $simulasi->margin;
                if($request->jenis_pinjaman_id == 1) {
                    $angsuran = (((($margin + $simulasi->biaya_admin) / 100) * $jumlah_pinjaman) / $simulasi->tenor );
                } else{
                    $angsuran = ($jumlah_pinjaman + ($jumlah_pinjaman * ($margin/100))) / $simulasi->tenor;
                }

                $totalPengembalian = $angsuran * $simulasi->tenor;

                $result = [
                    'tahun' => $simulasi->tahun_margin,
                    'tenor' => $simulasi->tenor,
                    'margin' => (float)$margin,
                    'biaya_admin' => (float)$simulasi->biaya_admin,
                    'biaya_admin_rp' => (float)$biaya_admin_rp,
                    'angsuran' => (float) number_format($angsuran, 2, '.', ''),
                    'total_pengembalian' => $totalPengembalian
                ];
            } else {
                return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => 'Data Not Found'], 404);
            }

            return $this->sendResponse($result, 'Data berhasil digenerate.');

        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
