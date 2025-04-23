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
                                            ->where('status', 'aktif')
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
                'jumlah_pinjaman' => 'required|integer',
                'tenor' => 'required|integer',
                'tahun' => 'required|integer'
            ],[
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
                                            ->where('tahun_margin', $request->tahun)
                                            ->where('status', 'aktif')
                                            ->first();

            $jumlah_pinjaman = $request->jumlah_pinjaman;

            if(!empty($simulasi)) {
                $margin = $simulasi->margin;
                $angsuran = ($jumlah_pinjaman + ($jumlah_pinjaman * ($margin/100))) / $simulasi->tenor;

                $result = [
                    'tahun' => $simulasi->tahun_margin,
                    'tenor' => $simulasi->tenor,
                    'margin' => (float)$margin,
                    'angsuran' => (float) number_format($angsuran, 2, '.', '')
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
