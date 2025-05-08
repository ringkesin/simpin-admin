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
        Schema::create('t_chat', function (Blueprint $table) {
            $table->ulid('t_chat_id')->primary();
            $table->string('ticket_code');
            $table->integer('p_chat_reference_table_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('subject')->nullable();
            $table->integer('status')->default(0)->comment('0 = Open, 1 = Closed');
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
        Schema::dropIfExists('t_chat');
    }
};
