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
            CREATE VIEW v_tabungan_saldo AS 
            SELECT
                a.p_anggota_id,
                a.nomor_anggota,
                a.nama,
                a.nik,
                CASE 
                    WHEN (
                        SELECT sum(b.total_sd) FROM t_tabungan_saldo b WHERE b.p_anggota_id = a.p_anggota_id GROUP BY b.tahun ORDER BY b.tahun DESC LIMIT 1
                    ) IS NOT NULL THEN (
                        SELECT sum(b.total_sd) FROM t_tabungan_saldo b WHERE b.p_anggota_id = a.p_anggota_id GROUP BY b.tahun ORDER BY b.tahun DESC LIMIT 1
                    )
                    ELSE 0
                END AS total_tabungan,
                CASE 
                    WHEN (
                        --gunakan jika di mysql : SELECT concat('Rp. ', REPLACE(format(sum(b.total_sd), 0), ',', '.')) FROM t_tabungan_saldo b WHERE b.p_anggota_id = a.p_anggota_id GROUP BY b.tahun ORDER BY b.tahun DESC LIMIT 1
                        SELECT _beautify_money(sum(b.total_sd)) FROM t_tabungan_saldo b WHERE b.p_anggota_id = a.p_anggota_id GROUP BY b.tahun ORDER BY b.tahun DESC LIMIT 1
                    ) IS NOT NULL THEN (
                        --gunakan jika di mysql : SELECT concat('Rp. ', REPLACE(format(sum(b.total_sd), 0), ',', '.')) FROM t_tabungan_saldo b WHERE b.p_anggota_id = a.p_anggota_id GROUP BY b.tahun ORDER BY b.tahun DESC LIMIT 1
                        SELECT _beautify_money(sum(b.total_sd)) FROM t_tabungan_saldo b WHERE b.p_anggota_id = a.p_anggota_id GROUP BY b.tahun ORDER BY b.tahun DESC LIMIT 1
                    )
                    ELSE 0::text
                END AS total_tabungan_beautify
            FROM  
                p_anggota a 
            WHERE 
                a.deleted_at IS NULL 
            ORDER BY 
                a.p_anggota_id DESC
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
            DROP VIEW IF EXISTS `v_tabungan_saldo`;
            SQL;
    }
};
