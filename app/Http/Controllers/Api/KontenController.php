<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Main\ContentModels;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class KontenController extends BaseController
{
    public function getAll()
    {
        try {
            $today = Carbon::today();
            $allData = ContentModels::where('valid_from', '<=', $today)
                                    ->where(function ($query) use ($today) {
                                        $query->whereNull('valid_to')
                                            ->orWhere('valid_to', '>=', $today);
                                    })
                                    ->orderByDesc('created_at')
                                    ->get();
            if (count($allData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            foreach($allData as $row) {
                $fileUrl = NULL;
                if ($row['thumbnail_path'] && Storage::disk('kkba_simpin')->exists($row['thumbnail_path'])) {
                    $fileUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $row['thumbnail_path']] // File path parameter
                    );
                }
                $row['thumbnail_path'] = $fileUrl;
            }

            $allData->makeHidden([
                'created_at',
                'updated_at',
            ]);
            return $this->sendResponse(['content' => $allData], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getActiveByTipe($tipe_content)
    {
        $today = Carbon::today();
        try {
            $allData = ContentModels::where('valid_from', '<=', $today)
                                    ->where(function ($query) use ($today) {
                                        $query->whereNull('valid_to')
                                            ->orWhere('valid_to', '>=', $today);
                                    })
                                    ->where('p_content_type_id', $tipe_content)
                                    ->orderByDesc('created_at')
                                    ->get();
            if (count($allData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            foreach($allData as $row) {
                $fileUrl = NULL;
                if ($row['thumbnail_path'] && Storage::disk('kkba_simpin')->exists($row['thumbnail_path'])) {
                    $fileUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $row['thumbnail_path']] // File path parameter
                    );
                }
                $row['thumbnail_path'] = $fileUrl;
            }

            $allData->makeHidden([
                'created_at',
                'updated_at',
            ]);
            return $this->sendResponse(['content' => $allData], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getById($id)
    {
        try{
            $allData = ContentModels::where('t_content_id', $id)->first();
            if (empty($allData)) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            $fileUrl = NULL;
            if ($allData['thumbnail_path'] && Storage::disk('kkba_simpin')->exists($allData['thumbnail_path'])) {
                $fileUrl = URL::temporarySignedRoute(
                    'secure-file', // Route name
                    now()->addMinutes(1), // Expiration time
                    ['path' => $allData['thumbnail_path']] // File path parameter
                );
            }
            $allData['thumbnail_path'] = $fileUrl;

            $allData->makeHidden([
                'created_at',
                'updated_at',
            ]);
            return $this->sendResponse(['content' => $allData], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function getGrid(Request $request)
    {
        try {
            $page = $request->page; // default page 1 jika tidak ada
            $perPage = $request->perpage;
            $offset = ($page - 1) * $perPage;
            $data = $request->data;

            $today = Carbon::today();
            $allData = ContentModels::where('valid_from', '<=', $today)
                                    ->where(function ($query) use ($today) {
                                        $query->whereNull('valid_to')
                                            ->orWhere('valid_to', '>=', $today);
                                    });

            if (isset($data['search'])) {
                $search = $data['search'];
                $allData = $allData->where(function ($query) use ($search) {
                    $query->where('content_title', 'like', '%' . $search . '%')
                            ->orWhere('content_text', 'like', '%' . $search . '%');
                });
            }

            $allData = $allData->offset($offset)
                                ->limit($perPage)
                                ->orderByDesc('created_at')
                                ->get();
            if (count($allData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            foreach($allData as $row) {
                $fileUrl = NULL;
                if ($row['thumbnail_path'] && Storage::disk('kkba_simpin')->exists($row['thumbnail_path'])) {
                    $fileUrl = URL::temporarySignedRoute(
                        'secure-file', // Route name
                        now()->addMinutes(1), // Expiration time
                        ['path' => $row['thumbnail_path']] // File path parameter
                    );
                }
                $row['thumbnail_path'] = $fileUrl;
            }

            $allData->makeHidden([
                'created_at',
                'updated_at',
            ]);
            return $this->sendResponse(['content' => $allData], 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}
