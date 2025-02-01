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
        Schema::create('rbac_role_menu_item', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('role_menu_id');
            $table->unsignedInteger('menu_item_id');
            $table->date('valid_from')->useCurrent();
            $table->date('valid_until')->nullable();
            $table->timestamps($precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('role_menu_id')
                ->references('id')->on('rbac_role_menu')->onDelete('restrict');
            $table->foreign('menu_item_id')
                ->references('id')->on('rbac_menu_item')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbac_role_menu_item');
    }
};
