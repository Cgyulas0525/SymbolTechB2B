<?php

require __DIR__ . "../../inc/utility.php";
require __DIR__ . "../../model/mySQLDatabase.php";
require __DIR__ . "../../model/productpricemodel.php";

class getProductPriceXML {

    public $fileName;
    public $utility = NULL;
    public $pdo = NULL;

    public $xmlArray = [];
    public $itemkeys = [];
    public $itemvalues = [];
    public $Product;


    function __construct($fileName) {
        date_default_timezone_set("Europe/Budapest");
        $this->fileName = $fileName;
        $this->utility = new Utility();
        $this->pdo = new mySQLDatabase();
    }

    public function getCurrency($currencyName) {
        return $this->pdo->fromNameToId('currency', $currencyName);
    }

    public function getQuantityUnit($quantityUnitName) {
        return $this->pdo->fromNameToId('quantityunit', $quantityUnitName);
    }

    public function insertRecord($PP)
    {
        $smtp = 'INSERT INTO productprice ( Product, Currency, ValidFrom, PriceCategory, QuantityUnit, Price, RowCreate, RowModify)
                   VALUES (' . $PP->Product . ',' . $PP->Currency .
            ', DATE_FORMAT("' . VALIDFROM . '", "%Y-%m-%d %H:%i:%s"),' .
            $PP->PriceCategory . ',' .
            $PP->QuantityUnit . ',' .
            $PP->Price . ',' .
            'DATE_FORMAT("' . DATE_NOW . '", "%Y-%m-%d %H:%i:%s"), ' .
            'DATE_FORMAT("' . DATE_NOW . '", "%Y-%m-%d %H:%i:%s"))';
        return $smtp;
    }

    public function updateRecord($PP)
    {
        $smtp = "UPDATE productprice
                    SET Price = " . $PP->Price . ",
                        RowModify = DATE_FORMAT('" . DATE_NOW . "', '%Y-%m-%d %H:%i:%s')
                 WHERE Validfrom = DATE_FORMAT( '" . VALIDFROM . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $PP->Currency .
                 " AND Product = " . $PP->Product . " AND PriceCategory = " . $PP->PriceCategory . " AND QuantityUnit = " . $PP->QuantityUnit;
        return $smtp;
    }

    public function isProductPriceToday($PP) {
        $sql = "SELECT Count(*) as db FROM productprice
                 WHERE Validfrom = DATE_FORMAT( '" . VALIDFROM . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $PP->Currency .
                       " AND Product = " . $PP->Product . " AND PriceCategory = " . $PP->PriceCategory . " AND QuantityUnit = " . $PP->QuantityUnit;
        return $this->pdo->countRecord($sql);
    }


    public function dbEvent($PP) {
        if ( $this->isProductPriceToday($PP) == 0) {
            $this->pdo->executeStatement($this->insertRecord($PP));
        } else {
            $this->pdo->executeStatement($this->updateRecord($PP));
        }
    }

    public function getPrice() {
        $this->xmlArray = $this->utility->fileLoader(PATH_XML . $this->fileName);
        $xmlArrayValues = array_values($this->xmlArray);
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
                                    $this->itemkeys = array_keys($vkkk[$n]);
                                    $this->itemvalues = array_values($vkkk[$n]);

                                    $PP = new productpricemodel();
                                    $PP->setProduct($this->Product);
                                    $PP->setRowCreate(DATE_NOW);
                                    $PP->setRowModify(DATE_NOW);

                                    for ( $o = 0; $o < count($this->itemvalues); $o++) {
                                        switch ($this->itemkeys[$o]) {
                                            case "pricecategory":
                                                $PP->setPriceCategory($this->itemvalues[$o]);
                                                break;
                                            case "value":
                                                $PP->setPrice($this->itemvalues[$o]);
                                                break;
                                            case "priceCurrency":
                                                $PP->setCurrency(self::getCurrency($this->itemvalues[$o]));
                                                break;
                                            case "quantityunit":
                                                $PP->setQuantityUnit(self::getQuantityUnit($this->itemvalues[$o]));
                                                break;
                                            case "validfrom":
                                                $PP->setValidfrom(VALIDFROM);
                                                break;
                                        }
                                    }
                                    if (!is_null($PP->QuantityUnit) && !is_null($PP->Currency)) {
                                        // vegyem e fel ha nincs?
                                        $this->dbEvent($PP);
                                    }
                                }
                            } else {
                                if ($kkk[$m] == "product" ) {
                                    $this->Product = $vkk[$m];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
