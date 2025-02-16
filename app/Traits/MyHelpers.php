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
}
