<?php

namespace App\Classes\Api\Inc;

class makeDateFormatClass
{
    /*
      * date format for sql
      *
      * @param $date - string
      *
      * @return string
      */
    public function sqlDate($date)
    {
        return "DATE_FORMAT('" . $date ."', '%Y-%m-%d %H:%i:%s')";
    }

    public function sqlDateAlter($date)
    {
        return 'DATE_FORMAT("' . $date .'", "%Y-%m-%d %H:%i:%s")';
    }

    public function makeDate($dateString)
    {
        $pos = strpos($dateString, 'T');
        $day = substr($dateString, 0, $pos);
        $time = substr($dateString, $pos + 1, 8);
        $date = $day . " ". $time;
        return $date;
    }

    public function makeSQLDate($dateString)
    {
        return $this->sqlDate($this->makeDate($dateString));
    }

    public function makeSQLDateAlter($dateString)
    {
        return $this->sqlDateAlter($this->makeDate($dateString));
    }
}
