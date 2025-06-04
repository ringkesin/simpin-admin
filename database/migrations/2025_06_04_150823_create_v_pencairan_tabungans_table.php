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
            CREATE VIEW v_pencairan_tabungan AS 
            SELECT 
                a.t_tabungan_pengambilan_id AS pencairan_id,
                a.p_anggota_id,
                b.nomor_anggota,
                b.nama AS nama_anggota,
                a.p_jenis_tabungan_id,
                c.nama AS jenis_tabungan,
                a.tgl_pengajuan,
                to_char(a.tgl_pengajuan, 'DD FMMonth YYYY, HH24:MI:SS'::text) AS tgl_pengajuan_beautify,
                a.jumlah_diambil,
                _beautify_money(a.jumlah_diambil) AS jumlah_diambil_beautify,
                a.jumlah_disetujui,
                _beautify_money(a.jumlah_disetujui) AS jumlah_disetujui_beautify,
                a.rekening_no,
                a.rekening_bank,
                a.status_pengambilan,
                a.tgl_pencairan,
                to_char(a.tgl_pencairan, 'DD FMMonth YYYY, HH24:MI:SS'::text) AS tgl_pencairan_beautify,
                a.catatan_user,
                a.catatan_approver
            FROM 
                t_tabungan_pengambilan a 
                LEFT JOIN p_anggota b ON b.p_anggota_id = a.p_anggota_id 
                LEFT JOIN p_jenis_tabungan c ON c.p_jenis_tabungan_id = a.p_jenis_tabungan_id
            WHERE 
                a.deleted_at IS NULL 
            ORDER BY 
                a.tgl_pengajuan DESC
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
            DROP VIEW IF EXISTS `v_pencairan_tabungan`;
            SQL;
    }
};
