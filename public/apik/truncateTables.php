<?php
require dirname(__DIR__, 1) . "/apik/inc/bootstrap.php";
require PATH_MODEL . "/mySQLDatabase.php";

$pdo = new mySQLDatabase();

$tables = $pdo->tablesName();

foreach ($tables as $table) {
    $value = array_values($table);
    if ($value[0] != 'dictionaries' && $value[0] != 'languages' && $value[0] != 'users') {
        $sql = "DELETE FROM " . $value[0];
        $pdo->executeStatement($sql);
    }
}
