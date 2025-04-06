<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Master\UnitModels;
use Exception;

class MasterUnitController extends BaseController
{
    public function getAll()
    {
        try {
            $allUnit = UnitModels::whereNull('deleted_at')->get();
            if (count($allUnit) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            $allUnit->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by',
            ]);
            return $this->sendResponse(['unit' => $allUnit], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}