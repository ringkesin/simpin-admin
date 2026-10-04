<?php

namespace App\Http\Controllers\Api;

use App\Models\Master\FeatureFlag;
use Illuminate\Http\JsonResponse;

class MasterFeatureFlagController extends BaseController
{
    public function getAll(): JsonResponse
    {
        $featureFlags = FeatureFlag::query()
            ->orderBy('id')
            ->get([
                'id',
                'code',
                'name',
                'is_enabled',
                'description',
                'coming_soon_message',
            ]);

        return $this->sendResponse(
            ['feature_flags' => $featureFlags],
            'Data feature flag berhasil diambil.'
        );
    }
}
