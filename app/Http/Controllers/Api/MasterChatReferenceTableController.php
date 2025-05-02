<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Master\ChatReferenceTableModels;

class MasterChatReferenceTableController extends BaseController
{
    public function getAll() {
        try {
            $allData = ChatReferenceTableModels::get();
            if (count($allData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            $allData->makeHidden([
                'created_at',
                'updated_at',
            ]);
            return $this->sendResponse($allData, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
