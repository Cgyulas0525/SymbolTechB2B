<?php
/*
* SÜ websyx ProductQuantities.xml processing
*/
require_once 'Utility.php';
require_once 'Database.php';

include 'warehousebalance.php';

class getProductQuantitiesXML {

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
     * Insert new warehousebalance record
     *
     * @param $wb - warehousebalance class
     *
     * @return string
     */
    public static function insertRecord($wb)
    {
        $smtp = 'INSERT INTO warehousebalance ( Product, Warehouse, Balance, AllocatedBalance, RowCreate, RowModify)
                   VALUES (' . $wb->Product . ',' . $wb->Warehouse . ',' .$wb->Balance . ',' .$wb->allocatedBalance . ',' .
            'DATE_FORMAT("' . self::$date . '", "%Y-%m-%d %H:%i:%s"), ' .
            'DATE_FORMAT("' . self::$date . '", "%Y-%m-%d %H:%i:%s"))';
        return $smtp;
    }

    /*
     * Modify warehousebalance record
     *
     * @param $wb - warehousebalance class
     *
     * @return string
     */
    public static function updateRecord($wb)
    {
        $smtp = "UPDATE warehousebalance
                    SET Balance = " . $wb->Balance . ",
                        AllocatedBalance = " . $wb->AllocatedBalance . ",
                        RowModify = DATE_FORMAT('" . self::$date . "', '%Y-%m-%d %H:%i:%s')
                  WHERE Product = " . $wb->Product . " AND Warehouse = " . $wb->Warehouse;
        return $smtp;
    }

    /*
     * there is this record
     *
     * @param $wb - warehousebalance class
     *
     * @return integer
     */
    public static function isWarehouseBalance($wb) {
        $sql = "SELECT Count(*) as db FROM warehousebalance
                 WHERE Product = " . $wb->Product . " AND Warehouse = " . $wb->Warehouse;
        return DB::countRecord($sql);
    }

    /*
     * xml row processing
     *
     * @param $PP - warehousebalance class
     *
     * @return void
     */
    public static function dbEvent($wb) {
        if ( self::isWarehouseBalance($wb) == 0) {
            DB::run(self::insertRecord($wb));
        } else {
            DB::run(self::updateRecord($wb));
        }
    }

    /*
     * xml json processing
     *
     * @return void
     */
    public static function getXML() {
        self::$xmlArray = Utility::fileLoader(self::$path);
        $xmlArrayValues = array_values(self::$xmlArray);
        $values = array_values($xmlArrayValues);
        for ($i = 0; $i < count($values); $i++ ) {
            $vals = array_values($values[$i]);
            for ( $j = 0; $j < count($vals); $j++) {
                self::$itemkeys = array_keys($vals[$j]);
                self::$itemvalues = array_values($vals[$j]);

                $wb = new warehousebalance();
                $wb->setRowCreate(self::$date);
                $wb->setRowModify(self::$date);

                for ( $k = 0; $k < count(self::$itemvalues); $k++) {
                    switch (self::$itemkeys[$k]) {
                        case "Warehouse":
                            $wb->setWarehouse(self::$itemvalues[$k]);
                            break;
                        case "Product":
                            $wb->setProduct(self::$itemvalues[$k]);
                            break;
                        case "Quantity":
                            $wb->setBalance(self::$itemvalues[$k]);
                            break;
                        case "StrictAllocate":
                            $wb->setAllocatedBalance(self::$itemvalues[$k] + self::$itemvalues[$k+1]);
                            break;
                    }
                }
                self::dbEvent($wb);
            }
        }
    }
}
