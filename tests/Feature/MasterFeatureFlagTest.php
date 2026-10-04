<?php

namespace Tests\Feature;

use App\Models\Master\FeatureFlag;
use App\Models\User;
use Database\Seeders\FeatureFlagSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MasterFeatureFlagTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Schema::connection('sqlite')->create('p_feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name', 150);
            $table->boolean('is_enabled')->default(false);
            $table->text('description')->nullable();
            $table->text('coming_soon_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        Sanctum::actingAs(new User([
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
        ]));
    }

    public function test_authenticated_user_can_get_all_feature_flags(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        FeatureFlag::create([
            'code' => 'another_feature',
            'name' => 'Another Feature',
            'is_enabled' => true,
        ]);

        $this->getJson('/api/master/feature-flags')
            ->assertOk()
            ->assertJsonCount(2, 'data.feature_flags')
            ->assertJsonPath('data.feature_flags.0.code', 'kkba_mart')
            ->assertJsonPath('data.feature_flags.0.is_enabled', false)
            ->assertJsonPath('data.feature_flags.0.coming_soon_message', 'Tunggu kehadiran kami, fitur akan segera launch.')
            ->assertJsonPath('data.feature_flags.1.is_enabled', true);
    }

    public function test_seeder_is_idempotent_and_does_not_disable_a_launched_feature(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        FeatureFlag::where('code', 'kkba_mart')->update(['is_enabled' => true]);

        $this->seed(FeatureFlagSeeder::class);

        $this->assertDatabaseCount('p_feature_flags', 1);
        $this->assertDatabaseHas('p_feature_flags', [
            'code' => 'kkba_mart',
            'name' => 'KKBA Mart',
            'is_enabled' => true,
            'coming_soon_message' => 'Tunggu kehadiran kami, fitur akan segera launch.',
        ]);
    }

    public function test_get_all_feature_flags_returns_an_empty_array(): void
    {
        $this->getJson('/api/master/feature-flags')
            ->assertOk()
            ->assertJsonCount(0, 'data.feature_flags');
    }
}
