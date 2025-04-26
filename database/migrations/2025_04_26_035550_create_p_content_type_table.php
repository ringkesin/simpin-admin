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
        Schema::create('p_content_type', function (Blueprint $table) {
            $table->bigIncrements('p_content_type_id');
            $table->string('content_code');
            $table->string('content_name');
            $table->timestamps($precision = 0);
            $table->softDeletes('deleted_at', precision: 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_content_type');
    }
};
