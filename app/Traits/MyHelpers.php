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
}
