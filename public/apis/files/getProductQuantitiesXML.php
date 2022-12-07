<?php

require __DIR__ . "../../inc/utility.php";
require __DIR__ . "../../model/mySQLDatabase.php";
require __DIR__ . "../../model/warehousebalance.php";

class getProductQuantitiesXML {

    public $fileName;
    public $utility = NULL;
    public $pdo = NULL;

    public $xmlArray = [];
    public $itemkeys = [];
    public $itemvalues = [];

    function __construct($fileName) {
        date_default_timezone_set("Europe/Budapest");
        $this->fileName = $fileName;
        $this->utility = new Utility();
        $this->pdo = new mySQLDatabase();
    }

    public function insertRecord($wb)
    {
        $smtp = 'INSERT INTO warehousebalance ( Product, Warehouse, Balance, AllocatedBalance, RowCreate, RowModify)
                   VALUES (' . $wb->Product . ',' . $wb->Warehouse . ',' .$wb->Balance . ',' .$wb->allocatedBalance . ',' .
            'DATE_FORMAT("' . DATE_NOW . '", "%Y-%m-%d %H:%i:%s"), ' .
            'DATE_FORMAT("' . DATE_NOW . '", "%Y-%m-%d %H:%i:%s"))';
        return $smtp;
    }

    public function updateRecord($wb)
    {
        $smtp = "UPDATE warehousebalance
                    SET Balance = " . $wb->Balance . ",
                        AllocatedBalance = " . $wb->AllocatedBalance . ",
                        RowModify = DATE_FORMAT('" . DATE_NOW . "', '%Y-%m-%d %H:%i:%s')
                  WHERE Product = " . $wb->Product . " AND Warehouse = " . $wb->Warehouse;
        return $smtp;
    }

    public function isWarehouseBalance($wb) {
        $sql = "SELECT Count(*) as db FROM warehousebalance
                 WHERE Product = " . $wb->Product . " AND Warehouse = " . $wb->Warehouse;
        return $this->pdo->countRecord($sql);
    }

    public function dbEvent($wb) {
        if ( $this->isWarehouseBalance($wb) == 0) {
            $this->pdo->executeStatement($this->insertRecord($wb));
        } else {
            $this->pdo->executeStatement($this->updateRecord($wb));
        }
    }


    public function getXML() {
        $this->xmlArray = $this->utility->fileLoader(PATH_XML . $this->fileName);
        $xmlArrayValues = array_values($this->xmlArray);
        $values = array_values($xmlArrayValues);
        for ($i = 0; $i < count($values); $i++ ) {
            $vals = array_values($values[$i]);
            for ( $j = 0; $j < count($vals); $j++) {
                $this->itemkeys = array_keys($vals[$j]);
                $this->itemvalues = array_values($vals[$j]);

                $wb = new warehousebalance();
                $wb->setRowCreate(DATE_NOW);
                $wb->setRowModify(DATE_NOW);

                for ( $k = 0; $k < count($this->itemvalues); $k++) {
                    switch ($this->itemkeys[$k]) {
                        case "Warehouse":
                            $wb->setWarehouse($this->itemvalues[$k]);
                            break;
                        case "Product":
                            $wb->setProduct($this->itemvalues[$k]);
                            break;
                        case "Quantity":
                            $wb->setBalance($this->itemvalues[$k]);
                            break;
                        case "StrictAllocate":
                            $wb->setAllocatedBalance($this->itemvalues[$k] + $this->itemvalues[$k+1]);
                            break;
                    }
                }
                $this->dbEvent($wb);
            }
        }
    }
}
