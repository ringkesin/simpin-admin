<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            AppsSeeder::class,
            AppsParamSeeder::class,
            MasterAnggotaSeeder::class,
            MasterJenisPinjamanSeeder::class,
            MasterStatusPengajuanSeeder::class,
            RbacRoleSeeder::class,
            RbacRoleUserSeeder::class,
            SimulasiPinjamanSeeder::class,
            MasterUnitSeeder::class,
            MasterPinjamanKeperluanSeeder::class,
            ContentTypeSeeder::class
        ]);
    }
}
