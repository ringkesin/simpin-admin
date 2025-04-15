<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Master\JenisPinjamanModels;
use Exception;

class MasterJenisPinjamanController extends BaseController
{
    public function getAll()
    {
        try {
            $allData = JenisPinjamanModels::get();
            if (count($allData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            $allData->makeHidden([
                'created_at',
                'updated_at',
            ]);
            return $this->sendResponse(['jenis_pinjaman' => $allData], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}