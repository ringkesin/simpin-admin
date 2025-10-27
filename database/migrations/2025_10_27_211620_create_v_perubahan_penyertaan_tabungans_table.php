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
            CREATE VIEW v_perubahan_penyertaan_tabungan AS
            SELECT
                a.t_tabungan_perubahan_penyertaan_id AS perubahan_penyertaan_id,
                a.p_anggota_id,
                b.nomor_anggota,
                b.nama AS nama_anggota,
                a.p_jenis_tabungan_id,
                c.nama AS jenis_tabungan,
                a.valid_from,
                to_char(a.valid_from, 'DD FMMonth YYYY'::text) AS tgl_mulai_beautify,
                a.nilai_sebelum,
                _beautify_money(a.nilai_sebelum) AS nilai_sebelum_beautify,
                a.nilai_baru,
                _beautify_money(a.nilai_baru) AS nilai_baru_beautify,
                a.status_perubahan_penyertaan,
                a.catatan_user,
                a.catatan_approver
            FROM
                t_tabungan_perubahan_penyertaan a
                LEFT JOIN p_anggota b ON b.p_anggota_id = a.p_anggota_id
                LEFT JOIN p_jenis_tabungan c ON c.p_jenis_tabungan_id = a.p_jenis_tabungan_id
            WHERE
                a.deleted_at IS NULL
            ORDER BY
                a.created_at DESC
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
            DROP VIEW IF EXISTS `v_perubahan_penyertaan_tabungan`;
            SQL;
    }
};
