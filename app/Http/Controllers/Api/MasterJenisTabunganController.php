<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Master\JenisTabunganModels;
use Exception;

class MasterJenisTabunganController extends BaseController
{
    public function getAll()
    {
        try {
            $allData = JenisTabunganModels::get();
            if (count($allData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            $allData->makeHidden([
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',
                'deleted_at',
                'deleted_by'
            ]);
            return $this->sendResponse(['jenis_tabungan' => $allData], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}