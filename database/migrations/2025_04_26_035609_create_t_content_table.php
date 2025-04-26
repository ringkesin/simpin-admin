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
        Schema::create('t_content', function (Blueprint $table) {
            $table->bigIncrements('t_content_id');
            $table->unsignedBigInteger('p_content_type_id');
            $table->string('thumbnail_path')->nullable();
            $table->string('content_title');
            $table->text('content_text')->nullable();
            $table->date('valid_from'); // Tanggal mulai berlaku
            $table->date('valid_to')->nullable(); // Tanggal berakhir (opsional)
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
        Schema::dropIfExists('t_content');
    }
};
