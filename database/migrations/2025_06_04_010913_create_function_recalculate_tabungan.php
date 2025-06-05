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
                _next_year INTEGER;
            BEGIN 		
                SELECT nilai_sd INTO _saldo_awal FROM t_tabungan_jurnal WHERE 
                p_anggota_id = _p_anggota_id AND date_part('year', tgl_transaksi) < _tahun AND deleted_at IS NULL 
                ORDER BY tgl_transaksi DESC, created_at DESC LIMIT 1;
                IF NOT FOUND THEN 
                    _saldo_awal := 0;
                END IF;
                
                _nilai_sd := _saldo_awal;
                FOR _jurnal IN 
                    SELECT t_tabungan_jurnal_id, nilai FROM t_tabungan_jurnal WHERE 
                    p_anggota_id = _p_anggota_id AND date_part('year', tgl_transaksi) = _tahun AND deleted_at IS NULL ORDER BY tgl_transaksi ASC 
                LOOP 
                    _nilai_sd := _nilai_sd + _jurnal.nilai;
                    UPDATE t_tabungan_jurnal SET nilai_sd = _nilai_sd WHERE t_tabungan_jurnal_id = _jurnal.t_tabungan_jurnal_id;
                END LOOP;

                FOR _jenis_tabungan IN 
                    SELECT p_jenis_tabungan_id FROM p_jenis_tabungan WHERE deleted_at IS NULL 
                LOOP
                    PERFORM t_tabungan_saldo_id FROM t_tabungan_saldo 
                    WHERE tahun = _tahun AND p_anggota_id = _p_anggota_id AND p_jenis_tabungan_id = _jenis_tabungan.p_jenis_tabungan_id AND deleted_at IS NULL LIMIT 1;
                    IF NOT FOUND THEN 
                        INSERT INTO t_tabungan_saldo (t_tabungan_saldo_id, p_anggota_id, p_jenis_tabungan_id, tahun, total_sd, created_at) VALUES (
                            replace(replace(replace(replace(replace(clock_timestamp()::text,'-',''),' ',''),':',''),'.',''),'+',''),
                            _p_anggota_id,
                            _jenis_tabungan.p_jenis_tabungan_id,
                            _tahun,
                            0,
                            now()
                        );
                    END IF;

                    SELECT sum(nilai) INTO _saldo_total FROM t_tabungan_jurnal WHERE 
                    p_anggota_id = _p_anggota_id 
                    AND p_jenis_tabungan_id = _jenis_tabungan.p_jenis_tabungan_id 
                    AND date_part('year', tgl_transaksi) <= _tahun  
                    AND deleted_at IS NULL;
                    
                    IF _saldo_total IS NULL THEN _saldo_total := 0; END IF;

                    UPDATE t_tabungan_saldo SET total_sd = _saldo_total WHERE 
                    p_anggota_id = _p_anggota_id 
                    AND p_jenis_tabungan_id = _jenis_tabungan.p_jenis_tabungan_id 
                    AND tahun = _tahun 
                    AND deleted_at IS NULL; 
                END LOOP;
                
                SELECT distinct(date_part('year', tgl_transaksi)) INTO _next_year FROM t_tabungan_jurnal 
                WHERE date_part('year', tgl_transaksi) > _tahun AND p_anggota_id = _p_anggota_id AND deleted_at IS NULL 
                ORDER BY date_part('year', tgl_transaksi) ASC LIMIT 1;
                IF FOUND THEN 
                    PERFORM _tabungan_recalculate(_p_anggota_id, _next_year);
                END IF;
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
