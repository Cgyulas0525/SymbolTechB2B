<?php

class MakeDateFormat {

    /*
     * date format for sql
     *
     * @param $date - string
     *
     * @return string
     */
    public static function sqlDate($date)
    {
        return "DATE_FORMAT('" . $date ."', '%Y-%m-%d %H:%i:%s')";
    }

    public static function makeDate($dateString)
    {
        $pos = strpos($dateString, 'T');
        $day = substr($dateString, 0, $pos);
        $time = substr($dateString, $pos + 1, 8);
        $date = $day . " ". $time;
        return $date;
    }

    public static function makeSQLDate($dateString)
    {
        return self::sqlDate(self::makeDate($dateString));
    }
}
