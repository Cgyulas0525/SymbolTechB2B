<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);

require __DIR__ . "/inc/bootstrap.php";
require __DIR__ . "/model/database.php";
require __DIR__ . "/model/mySQLDatabase.php";
require __DIR__ . "/model/modelRead.php";

$pdo = new database();
$mySQLPDO = new mySQLDatabase();
$model = new modelRead();

$modelNameArray = [
//"Currency",
//"CurrencyRate",
//"Customer",
//"CustomerAddress",
//"CustomerCategory",
//"CustomerContact",
//"CustomerOffer",
//"CustomerOfferCustomer",
//"CustomerOfferDetail",
//"CustomerOrder",
//"CustomerOrderStatus",
//"CustomerOrderDetail",
//"CustomerOrderDetailStatus",
//"Employee"
//"PaymentMethod",
//"PaymentMethodLang",
//"PriceCategory",
//"Product"
//"ProductAssociation",
//"ProductAssociationType",
//"ProductAttributes",
//"ProductAttribute",
//"ProductAttributeLang",
//"ProductCategory",
//"ProductCustomerCode",
//"ProductCustomerDiscount",
//"ProductLang",
//"ProductPrice",
//"QuantityUnit",
//"QuantityUnitLang",
//"SystemSetting",
//"SystemSettingValue",
//"TransportMode",
//"TransportModeLang",
//"Vat",
//"Warehouse",
//"WarehouseBalance",
//"WarehouseDailyBalance"
];

foreach ( $modelNameArray as $item ) {
    $datas = $pdo->modelSelect($item);
    echo $item . " " . count($datas) . "\n";
    $castArray = $model->castArray($item . '.php');
    $model->loader($datas, $item, $castArray, $mySQLPDO);
}

