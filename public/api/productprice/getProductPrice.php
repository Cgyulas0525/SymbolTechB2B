<?php

include 'getProductPriceXML.php';

$gPP = new getProductPriceXML('productprices.xml');

$gPP->getPrice();
