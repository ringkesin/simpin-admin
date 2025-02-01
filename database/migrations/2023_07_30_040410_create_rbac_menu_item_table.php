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
        Schema::create('rbac_menu_item', function (Blueprint $table) {
            // $table->string('id')->primary();
            $table->increments('id');
            $table->unsignedInteger('menu_id');
            // $table->string('parent_id')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('url')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('order_no');
            $table->text('remarks')->nullable();
            $table->date('valid_from')->useCurrent();
            $table->date('valid_until')->nullable();
            $table->timestamps($precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->foreign('menu_id')
                ->references('id')->on('rbac_menu')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbac_menu_item');
    }
};
