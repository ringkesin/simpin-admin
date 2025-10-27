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
            CREATE VIEW v_penyertaan_tabungan AS
            SELECT
                a.t_tabungan_penyertaan_id AS penyertaan_id,
                a.p_anggota_id,
                b.nomor_anggota,
                b.nama AS nama_anggota,
                a.p_jenis_tabungan_id,
                c.nama AS jenis_tabungan,
                a.penyertaan_date,
                to_char(a.penyertaan_date, 'DD FMMonth YYYY'::text) AS tgl_penyertaan_beautify,
                a.jumlah,
                _beautify_money(a.jumlah) AS jumlah_beautify,
                a.status_penyertaan,
                a.catatan_user,
                a.catatan_approver
            FROM
                t_tabungan_penyertaan a
                LEFT JOIN p_anggota b ON b.p_anggota_id = a.p_anggota_id
                LEFT JOIN p_jenis_tabungan c ON c.p_jenis_tabungan_id = a.p_jenis_tabungan_id
            WHERE
                a.deleted_at IS NULL
            ORDER BY
                a.penyertaan_date DESC
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
            DROP VIEW IF EXISTS `v_penyertaan_tabungan`;
            SQL;
    }
};
