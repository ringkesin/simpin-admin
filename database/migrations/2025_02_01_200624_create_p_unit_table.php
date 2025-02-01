<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('p_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unit_name');
            $table->string('kode_unit')->unique();
            $table->string('parent_id')->nullable();
            $table->string('location')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->boolean('is_project');
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_unit');
    }
};
