<?php

namespace Database\Seeders;

use App\Models\Master\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $featureFlag = FeatureFlag::firstOrCreate(
            ['code' => 'kkba_mart'],
            [
                'name' => 'KKBA Mart',
                'is_enabled' => false,
                'description' => 'Menentukan apakah fitur KKBA Mart sudah tersedia di aplikasi mobile.',
                'coming_soon_message' => 'Tunggu kehadiran kami, fitur akan segera launch.',
            ]
        );

        if ($featureFlag->coming_soon_message === null) {
            $featureFlag->update([
                'coming_soon_message' => 'Tunggu kehadiran kami, fitur akan segera launch.',
            ]);
        }
    }
}
