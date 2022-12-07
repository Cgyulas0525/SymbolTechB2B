<?php

require_once 'Utility.php';
include 'Database.php';

class getCurrencyArray
{
    public $bank;
    public static $url = 'http://api.napiarfolyam.hu?bank=';
    public static $currencyArray = [];
    public static $itemkeys = [];
    public static $itemvalues = [];

    public static $middleRate = 0;
    public static $purchaseRate = 0;
    public static $sellingRate = 0;
    public static $validfrom = NULL;
    public static $date = NULL;

    function __construct($bank) {
        date_default_timezone_set("Europe/Budapest");
        self::$validfrom = date('Y-m-d H:i:s', strtotime('midnight'));
        self::$date = date('Y-m-d H:i:s', strtotime('now'));

        $this->bank = $bank;
        self::$url = self::$url . $this->bank;
    }

    public static function arrayItem($mit) {
        return array_search($mit , self::$itemkeys);
    }

    public static function currencyRateUpdate($id) {
        $smtp = "UPDATE currencyrate
                    SET Rate = " . self::$middleRate . ",
                        RateBuy = " . self::$purchaseRate . ",
                        RateSell = " . self::$sellingRate . ",
                        RowModify = DATE_FORMAT('" . self::$date . "', '%Y-%m-%d %H:%i:%s')
                 WHERE Validfrom = DATE_FORMAT( '" . self::$validfrom . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $id;
        return $smtp;
    }

    public static function currencyRateInsert($id) {
        $smtp = 'INSERT INTO currencyrate ( Currency, ValidFrom, Rate, RateBuy, RateSell, RowCreate, RowModify)
                   VALUES (' . $id .
                            ', DATE_FORMAT("' . self::$validfrom . '", "%Y-%m-%d %H:%i:%s"),' .
                            self::$middleRate . ',' .
                            self::$purchaseRate . ',' .
                            self::$sellingRate . ',' .
                            'DATE_FORMAT("' . self::$date . '", "%Y-%m-%d %H:%i:%s"), ' .
                            'DATE_FORMAT("' . self::$date . '", "%Y-%m-%d %H:%i:%s"))';
        return $smtp;
    }

    public static function isCurrencyRateToday($id) {
        $sql = "SELECT Count(*) as db FROM currencyrate WHERE Validfrom = DATE_FORMAT( '" . self::$validfrom . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $id;
        return DB::countRecord($sql);
    }

    public static function itemValues() {
        if (self::arrayItem('kozep')) {
            self::$purchaseRate = array_values(self::$itemvalues[self::arrayItem('kozep')])[0];
            self::$sellingRate = array_values(self::$itemvalues[self::arrayItem('kozep')])[0];
            self::$middleRate = array_values(self::$itemvalues[self::arrayItem('kozep')])[0];
        } else {
            self::$purchaseRate = self::$itemvalues[self::arrayItem('vetel')];
            self::$sellingRate = self::$itemvalues[self::arrayItem('eladas')];
            self::$middleRate = (self::$purchaseRate + self::$sellingRate) / 2;
        }
    }

    public static function dbEvent() {
        $sql = "SELECT Id FROM currency WHERE Name = '" . self::$itemvalues[self::arrayItem('penznem')] . "'";
        $smtp = DB::run($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) > 0) {
                foreach ($record as $row) {
                    if (self::isCurrencyRateToday(intval($row['Id'])) == 0) {
                        DB::run(self::currencyRateInsert(intval($row['Id'])));
                    } else {
                        DB::run(self::currencyRateUpdate(intval($row['Id'])));
                    }
                }
            }
        }
    }

    public static function getArray()
    {

        self::$currencyArray = Utility::fileLoader(self::$url);
        $values = array_values(self::$currencyArray);

        $values = array_values($values);
        $values = array_values($values[1]);
        $values = array_values($values);

        for ( $i = 0; $i < count($values); $i++) {

            $arrayValues = array_values($values[$i]);

            for ( $j = 0; $j < count($arrayValues); $j++) {
                self::$itemkeys = array_keys($arrayValues[$j]);
                self::$itemvalues = array_values($arrayValues[$j]);

                self::itemValues();
                self::dbEvent();
            }
        }
    }
}
