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
        Schema::create('rbac_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('apps_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedInteger('order_no');
            $table->text('remarks')->nullable();
            $table->date('valid_from')->useCurrent();
            $table->date('valid_until')->nullable();
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();

            $table->foreign('apps_id')
                ->references('id')->on('apps')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbac_menu');
    }
};
