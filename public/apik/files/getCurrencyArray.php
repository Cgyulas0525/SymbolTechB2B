<?php

//require PATH_INC . "/utility.php";
require PATH_MODEL . "/mySQLDatabase.php";
require PATH_MODEL .'/api.php';
require PATH_MODEL . "/apimodel.php";

class getCurrencyArray
{
    public $bank;
    public $url = CURRENCYRATE_URL;
    public $currencyArray = [];
    public $itemkeys = [];
    public $itemvalues = [];

    public $middleRate = 0;
    public $purchaseRate = 0;
    public $sellingRate = 0;

    public $validfrom = NULL;
    public $date = NULL;
    public $utility = NULL;
    public $pdo = NULL;

    function __construct($bank) {
        $this->utility = new Utility();
        $this->pdo = new mySQLDatabase();
        $this->bank = $bank;
        $this->url .= $this->bank;
        date_default_timezone_set("Europe/Budapest");
        $this->validfrom = date('Y-m-d H:i:s', strtotime('midnight'));
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
        $this->api = new api();
        $this->apimodel = new apimodel();
    }

    public function getArray()
    {
        $this->currencyArray = $this->utility->fileLoader($this->url);
        $values = array_values($this->currencyArray);

        $values = array_values($values);
        $values = array_values($values[1]);
        $values = array_values($values);

        $this->api->insert($this->url);

        $this->apimodel->api_id = $this->api->id;
        $this->apimodel->model = 'CurrencyRate';
        $this->apimodel->recordnumber = 0;
        $this->apimodel->insertednumber = 0;
        $this->apimodel->updatednumber = 0;
        $this->apimodel->errornumber = 0;

        $this->apimodel->insert();
        $this->apimodel->id = $this->apimodel->selectId();

        for ( $i = 0; $i < count($values); $i++) {

            $arrayValues = array_values($values[$i]);

            for ( $j = 0; $j < count($arrayValues); $j++) {
                $this->itemkeys = array_keys($arrayValues[$j]);
                $this->itemvalues = array_values($arrayValues[$j]);

                $this->itemValues();
                $this->dbEvent();
            }
        }

        $this->apimodel->updateErrornumber();
    }


    public function arrayItem($mit) {
        return array_search($mit , $this->itemkeys);
    }

    public function currencyRateUpdate($id) {
        $this->apimodel->recordnumber++;
        $this->apimodel->updatednumber++;
        $smtp = "UPDATE currencyrate
                    SET Rate = " . $this->middleRate . ",
                        RateBuy = " . $this->purchaseRate . ",
                        RateSell = " . $this->sellingRate . ",
                        RowModify = DATE_FORMAT('" . $this->date . "', '%Y-%m-%d %H:%i:%s')
                 WHERE Validfrom = DATE_FORMAT( '" . $this->validfrom . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $id;
        return $smtp;
    }

    public function currencyRateInsert($id) {
        $this->apimodel->recordnumber++;
        $this->apimodel->insertednumber++;
        $smtp = 'INSERT INTO currencyrate ( Currency, ValidFrom, Rate, RateBuy, RateSell, RowCreate, RowModify)
                   VALUES (' . $id .
                            ', DATE_FORMAT("' . $this->validfrom . '", "%Y-%m-%d %H:%i:%s"),' .
                            $this->middleRate . ',' .
                            $this->purchaseRate . ',' .
                            $this->sellingRate . ',' .
                            'DATE_FORMAT("' . $this->date . '", "%Y-%m-%d %H:%i:%s"), ' .
                            'DATE_FORMAT("' . $this->date . '", "%Y-%m-%d %H:%i:%s"))';
        return $smtp;
    }

    public function isCurrencyRateToday($id) {
        $where = "Validfrom = DATE_FORMAT( '" . $this->validfrom . "', '%Y-%m-%d %H:%i:%s') AND Currency = " . $id;
        return $this->pdo->countRecord( 'currencyrate', $where);
    }

    public function itemValues() {
        if ($this->arrayItem('kozep')) {
            $this->purchaseRate = array_values($this->itemvalues[self::arrayItem('kozep')])[0];
            $this->sellingRate = array_values($this->itemvalues[self::arrayItem('kozep')])[0];
            $this->middleRate = array_values($this->itemvalues[self::arrayItem('kozep')])[0];
        } else {
            $this->purchaseRate = $this->itemvalues[self::arrayItem('vetel')];
            $this->sellingRate = $this->itemvalues[self::arrayItem('eladas')];
            $this->middleRate = ($this->purchaseRate + $this->sellingRate) / 2;
        }
    }

    public function dbEvent() {
        $sql = "SELECT Id FROM currency WHERE Name = '" . $this->itemvalues[$this->arrayItem('penznem')] . "'";
        $smtp = $this->pdo->executeStatement($sql);
        if ($smtp) {
            $record = $smtp->fetchAll();
            if (count($record) > 0) {
                foreach ($record as $row) {
                    if ($this->isCurrencyRateToday(intval($row['Id'])) == 0) {
                        $this->pdo->executeStatement($this->currencyRateInsert(intval($row['Id'])));
                    } else {
                        $this->pdo->executeStatement($this->currencyRateUpdate(intval($row['Id'])));
                    }
                }
            }
        }
    }

}
