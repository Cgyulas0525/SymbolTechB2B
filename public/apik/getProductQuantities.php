<?php

require __DIR__ . "/inc/bootstrap.php";
require __DIR__ ."/files/getProductQuantitiesXML.php";

$getProductQuantities = new getProductQuantitiesXML('productquantities.xml');
$getProductQuantities->getXML();
