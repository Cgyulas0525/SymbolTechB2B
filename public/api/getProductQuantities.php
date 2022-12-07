<?php
/*
* SÜ websyx ProductQuantities.xml processing
*/
include 'getProductQuantitiesXML.php';

$gPP = new getProductQuantitiesXML('productquantities.xml');

$gPP->getXML();
