<?php

/*
 * SÜ websyx ProductPrice.xml processing
 */
include 'getProductPriceXML.php';

$gPP = new getProductPriceXML('productprices.xml');

$gPP->getPrice();
