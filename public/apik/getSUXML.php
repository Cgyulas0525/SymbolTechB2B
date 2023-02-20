<?php
require __DIR__ . "/inc/bootstrap.php";
require PATH_FILES . "/GetXmls.php";

$xml = new XML();
$xml->xmlLoader();
