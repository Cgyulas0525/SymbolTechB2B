<?php
require dirname(__DIR__, 1) . "/apik/inc/bootstrap.php";
require PATH_MODEL . "/mySQLDatabase.php";

$pdo = new mySQLDatabase();

date_default_timezone_set("Europe/Budapest");

$outputFile = fopen(PATH_OUTPUT . 'truncate-' . uniqid() . '.txt', "w") or die("Unable to open file!");
$txt = "B2B TRUNCATE\n";
fwrite($outputFile, $txt);
$txt = "Start: " .date('Y.m.d h:m:s', strtotime('now')) . "\n";
fwrite($outputFile, $txt);

$tables = $pdo->tablesName();

foreach ($tables as $table) {
    $value = array_values($table);
    if ($value[0] != 'dictionaries' && $value[0] != 'languages' && $value[0] != 'users') {
        $sql = "DELETE FROM " . $value[0];
        $pdo->executeStatement($sql);
    }
}


$txt = "End: " .date('Y.m.d h:m:s', strtotime('now')) . "\n";
fwrite($outputFile, $txt);
$txt = "OK\n";
fwrite($outputFile, $txt);
fclose($outputFile);


