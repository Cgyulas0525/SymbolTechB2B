<?php

include 'getProductQuantitiesXML.php';

$gPP = new getProductQuantitiesXML('productquantities.xml');

$gPP->getXML();
