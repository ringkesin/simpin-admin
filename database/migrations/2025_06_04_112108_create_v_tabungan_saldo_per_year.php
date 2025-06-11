<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement($this->dropView());
    }

    private function createView(): string
    {
        return <<<SQL
            CREATE VIEW v_tabungan_saldo_per_year AS 
            SELECT 
                a.t_tabungan_saldo_id,
                a.p_anggota_id,
                a.p_jenis_tabungan_id,
                b.nama,
                a.tahun,
                a.total,
                _beautify_money(a.total) AS total_beautify,
                a.total_sd,
                _beautify_money(a.total_sd) AS total_sd_beautify 
            FROM 
                t_tabungan_saldo a 
                LEFT JOIN p_jenis_tabungan b ON b.p_jenis_tabungan_id = a.p_jenis_tabungan_id 
            WHERE 
                a.deleted_at IS NULL 
            ORDER BY 
                a.tahun DESC, a.p_jenis_tabungan_id ASC
            SQL;
    }

    /**
     * Delete view
     *
     * @return void
     */
    private function dropView(): string
    {
        return <<<SQL
            DROP VIEW IF EXISTS `v_tabungan_saldo_per_year`;
            SQL;
    }
};
