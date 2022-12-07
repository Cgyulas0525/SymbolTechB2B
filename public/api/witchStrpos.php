<?php

class witchStrpos {

    static $howMany = 0;
    static $actPos = 0;
    static $returnPos = 0;

    public static function withcPos($inWhat, $what, $witch) {
        $pos = strpos($inWhat, $what);
        self::$actPos = self::$actPos + $pos;
        $string = substr($inWhat, $pos + 1);
        self::$howMany++;
        if (self::$howMany == $witch) {
            self::$returnPos = self::$actPos;
            self::init();
        } else {
            self::withcPos($string, $what, $witch);
        }
    }

    public static function getReturnPos() {
        return self::$returnPos;
    }

    public static function init() {
        self::$actPos = 0;
        self::$howMany = 0;
    }

    public static function getPos($string, $witch)
    {
        self::withcPos($string , "'", $witch);
        return self::$returnPos;
    }

    public static function getSubstrMark($string, $witch)
    {
        $beginPos = self::getPos($string, $witch);
        $endPos = self::getPos($string, $witch + 1);

        return substr($string, $beginPos + ($witch - 1) , ($endPos + 2) - $beginPos);
    }

    public static function getSubstr($string, $witch)
    {
        $beginPos = self::getPos($string, $witch);
        $endPos = self::getPos($string, $witch + 1);

        return substr($string, $beginPos + $witch , $endPos  - $beginPos);
    }

}


