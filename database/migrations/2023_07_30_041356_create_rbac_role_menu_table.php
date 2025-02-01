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
        Schema::create('rbac_role_menu', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('menu_id');
            $table->date('valid_from')->useCurrent();
            $table->date('valid_until')->nullable();
            $table->timestamps($precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('role_id')
                ->references('id')->on('rbac_role')->onDelete('restrict');
            $table->foreign('menu_id')
                ->references('id')->on('rbac_menu')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbac_role_menu');
    }
};
