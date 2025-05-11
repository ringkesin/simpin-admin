<?php

namespace App\Traits;

trait MyHelpers
{
    function setIfNull($var, $desiredString = '-')
    {
        return (empty($var)) ? $desiredString : $var;
    }

    function toDate($var, $format = 'd-M-Y', $desiredString = '-')
    {
        $date = $this->setIfNull($var, $desiredString);
        if ($date == $desiredString) {
            return $date;
        }
        return date($format, strtotime($var));
    }

    function toRupiah($angka)
    {
        $angka = number_format($angka, 2);
        $angka = str_replace('.', '#koma#', $angka);
        $angka = str_replace(',', '.', $angka);
        $angka = str_replace('#koma#', ',', $angka);

        $new_angka = $angka;
        $pecah = explode(',', $angka);
        if (count($pecah) > 0) {
            if ($pecah[1] == '00') {
                $new_angka = $pecah[0];
            }
        }
        return $new_angka;
    }

    function toMonth($var, $desiredString = '-')
    {
        $month = [
            1 => "Januari",
            2 => "Februari",
            3 => "Maret",
            4 => "April",
            5 => "Mei",
            6 => "Juni",
            7 => "Juli",
            8 => "Agustus",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Desember"
        ];

        return $month[$var] ?? $desiredString;
    }

    function listMonth()
    {
        $month = [
            1 => "Januari",
            2 => "Februari",
            3 => "Maret",
            4 => "April",
            5 => "Mei",
            6 => "Juni",
            7 => "Juli",
            8 => "Agustus",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Desember"
        ];

        return $month;
    }

    public static function getYearRange($range = 10)
    {
        $currentYear = date('Y');
        $startYear = $currentYear - $range;
        $endYear = $currentYear + $range;

        return range($startYear, $endYear);
    }

    function generateRandomCode($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }

    function bulanKeRomawi($bulan) {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romawi[$bulan] ?? 'Invalid';
    }
}
