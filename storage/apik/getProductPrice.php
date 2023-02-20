<?php

require __DIR__ . "/inc/bootstrap.php";
require __DIR__ ."/files/getProductPriceXML.php";

$getProductPrice = new getProductPriceXML('productprices.xml');
$getProductPrice->getPrice();


