<?php

/*
 * SÜ websyx ProductPrice.xml processing
 */
require_once 'Utility.php';
require_once 'Database.php';
include 'productprice.php';

class getProductPriceXML {

    public $fileName;
    public static $path = NULL;
    public static $xmlArray = [];
    public static $itemkeys = [];
    public static $itemvalues = [];
    public static $validfrom = NULL;
    public static $date = NULL;
    public static $Product;


    function __construct($fileName) {
        date_default_timezone_set("Europe/Budapest");
        self::$validfrom = date('Y-m-d H:i:s', strtotime('midnight'));
        self::$date = date('Y-m-d H:i:s', strtotime('now'));

        $this->fileName = $fileName;
        self::$path = dirname(__DIR__,2) . '/public/xml/';
        self::$path = self::$path . $this->fileName;
    }

    /*
     * Currency Id from Name
     *
     * @param $currencyName - string
     *
     * @return integer
    */

    public static function getCurrency($currencyName) {
        return DB::fromNameToId('currency', $currencyName);
    }

    /*
     * Quantity Id from Name
     *
     * @param $quantityUnitName - string
     *
     * @return integer
     */
    public static function getQuantityUnit($quantityUnitName) {
        return DB::fromNameToId('quantityunit', $quantityUnitName);
    }

    /*
     * Insert new productprice record
     *
     * @param $PP - productprice class
     *
     * @return string
     */
    public static function insertRecord($PP)
    {
        $smtp = 'INSERT INTO productprice ( Product, Currency, ValidFrom, PriceCategory, QuantityUnit, Price, RowCreate, RowModify)
                   VALUES (' . $PP->Product . ',' . $PP->Currency .
            ', DATE_FORMAT("' . self::$validfrom . '", "%Y-%m-%d %H:%i:%s"),' .
            $PP->PriceCategory . ',' .
            $PP->QuantityUnit . ',' .
            $PP->Price . ',' .
            'DATE_FORMAT("' . self::$date . '", "%Y-%m-%d %H:%i:%s"), ' .
            'DATE_FORMAT("' . self::$date . '", "%Y-%m-%d %H:%i:%s"))';
        return $smtp;
    }

    /*
     * Modify productprice record
     *
     * @param $PP - productprice class
     *
     * @return string
     */
    public static function updateRecord($PP)
    {
        $smtp = "UPDATE productprice
                    SET Price = " . $PP->Price . ",
                        RowModify = DATE_FORMAT('" . self::$date . "', '%Y-%m-%d %H:%i:%s')
                 WHERE Validfrom = DATE_FORMAT( '" . self::$validfrom . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $PP->Currency .
                 " AND Product = " . $PP->Product . " AND PriceCategory = " . $PP->PriceCategory . " AND QuantityUnit = " . $PP->QuantityUnit;
        return $smtp;
    }

    /*
     * is there today's exchange rate?
     *
     * @param $PP - productprice class
     *
     * @return integer
     */
    public static function isProductPriceToday($PP) {
        $sql = "SELECT Count(*) as db FROM productprice
                 WHERE Validfrom = DATE_FORMAT( '" . self::$validfrom . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $PP->Currency .
                       " AND Product = " . $PP->Product . " AND PriceCategory = " . $PP->PriceCategory . " AND QuantityUnit = " . $PP->QuantityUnit;
        return DB::countRecord($sql);
    }

    /*
     * xml row processing
     *
     * @param $PP - productprice class
     *
     * @return void
     */
    public static function dbEvent($PP) {
        if ( self::isProductPriceToday($PP) == 0) {
            DB::run(self::insertRecord($PP));
        } else {
            DB::run(self::updateRecord($PP));
        }
    }

    /*
     * xml json processing
     *
     * @return void
     */
    public static function getPrice() {
        self::$xmlArray = Utility::fileLoader(self::$path);
        $xmlArrayValues = array_values(self::$xmlArray);
        for ( $i = 0; $i < count($xmlArrayValues); $i++) {
            $values = array_values($xmlArrayValues);
            for ( $j = 0; $j < count($values); $j++) {
                $val = array_values($values);
                for ( $k = 0; $k < count($val); $k++) {
                    $vk = array_values($val[$k]);
                    for ($l = 0; $l < count($vk); $l++) {
                        $kkk = array_keys($vk[$l]);
                        $vkk = array_values($vk[$l]);
                        for ( $m = 0; $m < count($vkk); $m++) {
                            if (is_array($vkk[$m])) {
                                $vkkk = array_values($vkk[$m]);
                                for ( $n = 0; $n < count($vkkk); $n++) {
                                    self::$itemkeys = array_keys($vkkk[$n]);
                                    self::$itemvalues = array_values($vkkk[$n]);

                                    $PP = new productprice();
                                    $PP->setProduct(self::$Product);
                                    $PP->setRowCreate(self::$date);
                                    $PP->setRowModify(self::$date);

                                    for ( $o = 0; $o < count(self::$itemvalues); $o++) {
                                        switch (self::$itemkeys[$o]) {
                                            case "pricecategory":
                                                $PP->setPriceCategory(self::$itemvalues[$o]);
                                                break;
                                            case "value":
                                                $PP->setPrice(self::$itemvalues[$o]);
                                                break;
                                            case "priceCurrency":
                                                $PP->setCurrency(self::getCurrency(self::$itemvalues[$o]));
                                                break;
                                            case "quantityunit":
                                                $PP->setQuantityUnit(self::getQuantityUnit(self::$itemvalues[$o]));
                                                break;
                                            case "validfrom":
                                                $PP->setValidfrom(self::$validfrom);
                                                break;
                                        }
                                    }
                                    if (!is_null($PP->QuantityUnit) && !is_null($PP->Currency)) {
                                        // vegyem e fel ha nincs?
                                        self::dbEvent($PP);
                                    }
                                }
                            } else {
                                if ($kkk[$m] == "product" ) {
                                    self::$Product = $vkk[$m];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
