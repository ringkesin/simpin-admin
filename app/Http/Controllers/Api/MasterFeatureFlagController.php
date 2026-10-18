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
                'allowed_anggota_ids',
            ])
            ->map(function (FeatureFlag $featureFlag): FeatureFlag {
                $featureFlag->allowed_anggota_ids = $featureFlag->allowed_anggota_ids ?? [];

                return $featureFlag;
            });

        return $this->sendResponse(
            ['feature_flags' => $featureFlags],
            'Data feature flag berhasil diambil.'
        );
    }
}
