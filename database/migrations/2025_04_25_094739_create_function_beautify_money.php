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
            CREATE OR REPLACE FUNCTION _beautify_money(double precision)
            RETURNS character varying
            LANGUAGE plpgsql
            AS \$function\$
            DECLARE 
                _money ALIAS FOR \$1;
                _beautify VARCHAR;
                _decimal VARCHAR;
            BEGIN 
                SELECT trim(to_char(_money,'99 999 999 999 999 990D99')) INTO _beautify;
                _beautify := replace(_beautify, '.', ',');
                _beautify := replace(_beautify, ' ', '.');
                _decimal := split_part(_beautify, ',', 2);
                IF _decimal = '00' THEN 
                    _beautify :=  replace(_beautify, ',00', '');
                END IF;
                RETURN _beautify;
            END; 
            \$function\$;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS _beautify_money(double precision);');
    }
};
