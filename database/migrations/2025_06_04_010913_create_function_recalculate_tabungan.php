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
        DB::unprepared(<<<SQL
            CREATE OR REPLACE FUNCTION _tabungan_recalculate(integer, integer)
            RETURNS void
            LANGUAGE plpgsql
            AS \$function\$
            DECLARE
                _p_anggota_id ALIAS FOR $1;
                _tahun ALIAS FOR $2;
                
                _jenis_tabungan RECORD;
                _jurnal RECORD;

                _saldo_awal FLOAT := 0;
                _nilai_sd FLOAT := 0;
                _saldo_total FLOAT := 0;
                _saldo_sd_total FLOAT := 0;
                
                _tahun_start INTEGER;
                _tahun_end INTEGER := EXTRACT(YEAR FROM CURRENT_DATE);
            BEGIN 		
                -- Get saldo awal dari tahun sebelumnya ---------------------- 		
                SELECT nilai_sd INTO _saldo_awal FROM t_tabungan_jurnal WHERE 
                p_anggota_id = _p_anggota_id AND date_part('year', tgl_transaksi) < _tahun AND deleted_at IS NULL 
                ORDER BY tgl_transaksi DESC, created_at DESC LIMIT 1;
                IF NOT FOUND THEN 
                    _saldo_awal := 0;
                END IF;
                
                -- Loop Jurnal dari tahun terpilih s.d saat ini ----------------------
                _nilai_sd := _saldo_awal;
                FOR _jurnal IN 
                    SELECT t_tabungan_jurnal_id, date_part('year', tgl_transaksi) AS tahun, p_jenis_tabungan_id, nilai FROM t_tabungan_jurnal WHERE 
                    p_anggota_id = _p_anggota_id AND date_part('year', tgl_transaksi) >= _tahun AND deleted_at IS NULL ORDER BY tgl_transaksi ASC 
                LOOP 
                    _nilai_sd := _nilai_sd + _jurnal.nilai;
                    UPDATE t_tabungan_jurnal SET nilai_sd = _nilai_sd WHERE t_tabungan_jurnal_id = _jurnal.t_tabungan_jurnal_id;
                END LOOP;

                -- Reset saldo dari tahun terpilih s.d saat ini ----------------------
                DELETE FROM t_tabungan_saldo WHERE p_anggota_id = _p_anggota_id AND tahun >= _tahun;	
                _tahun_start := _tahun;
                WHILE _tahun_start <= _tahun_end LOOP
                    FOR _jenis_tabungan IN 
                        SELECT p_jenis_tabungan_id FROM p_jenis_tabungan --WHERE deleted_at IS NULL 
                    LOOP
                        SELECT sum(nilai) INTO _saldo_total FROM t_tabungan_jurnal WHERE 
                        p_anggota_id = _p_anggota_id AND p_jenis_tabungan_id = _jenis_tabungan.p_jenis_tabungan_id AND date_part('year', tgl_transaksi) = _tahun_start AND deleted_at IS NULL;
                        IF _saldo_total IS NULL THEN _saldo_total := 0; END IF;

                        SELECT sum(nilai) INTO _saldo_sd_total FROM t_tabungan_jurnal WHERE 
                        p_anggota_id = _p_anggota_id AND p_jenis_tabungan_id = _jenis_tabungan.p_jenis_tabungan_id AND date_part('year', tgl_transaksi) <= _tahun_start AND deleted_at IS NULL;
                        IF _saldo_sd_total IS NULL THEN _saldo_sd_total := 0; END IF;

                        INSERT INTO t_tabungan_saldo (t_tabungan_saldo_id, p_anggota_id, p_jenis_tabungan_id, tahun, total, total_sd, created_at) VALUES (
                            replace(replace(replace(replace(replace(clock_timestamp()::text,'-',''),' ',''),':',''),'.',''),'+',''),
                            _p_anggota_id,
                            _jenis_tabungan.p_jenis_tabungan_id,
                            _tahun_start,
                            _saldo_total,
                            _saldo_sd_total,
                            now()
                        );
                    END LOOP;
                    _tahun_start := _tahun_start + 1;
                END LOOP;
            END; 
            \$function\$;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS _tabungan_recalculate(integer, integer);');
    }
};
