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
            CREATE VIEW v_tabungan_jurnal AS 
            SELECT 
                a.t_tabungan_jurnal_id,
                a.created_at,
                date_format(a.created_at, '%d-%b-%Y %H:%i:%s') AS created_at_beautify,
                a.tgl_transaksi,
                date_format(a.tgl_transaksi, '%d-%b-%Y %H:%i:%s') AS tgl_transaksi_beautify,
                date_format(a.tgl_transaksi, '%c') AS bulan_transaksi,
                date_format(a.tgl_transaksi, '%Y') AS tahun_transaksi,
                a.p_anggota_id,
                c.nama AS anggota_name,
                c.nomor_anggota AS anggota_nomor,
                a.p_jenis_tabungan_id,
                b.nama AS jenis_tabungan,
                a.nilai,
                concat('Rp. ', REPLACE(format(a.nilai, 0), ',', '.')) AS nilai_beautify,
                a.nilai_sd,
                concat('Rp. ', REPLACE(format(a.nilai_sd, 0), ',', '.')) AS nilai_sd_beautify,
                a.catatan
            FROM 
                t_tabungan_jurnal a 
                LEFT JOIN p_jenis_tabungan b ON b.p_jenis_tabungan_id = a.p_jenis_tabungan_id 
                LEFT JOIN p_anggota c ON c.p_anggota_id = a.p_anggota_id 
            WHERE 
                a.deleted_at IS NULL 
            ORDER BY 
                a.tgl_transaksi DESC
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
            DROP VIEW IF EXISTS `v_tabungan_jurnal`;
            SQL;
    }
};
