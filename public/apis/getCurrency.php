<?php
require __DIR__ . "/inc/bootstrap.php";
require __DIR__ ."/files/getCurrencyArray.php";

$getCurrencyArray = new getCurrencyArray('mnb');
$getCurrencyArray->getArray();
