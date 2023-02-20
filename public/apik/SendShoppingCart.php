<?php
require __DIR__ . "/inc/bootstrap.php";
//require PATH_INC . "/utility.php";
require PATH_INC . "/ModelChange.php";
require PATH_MODEL . "/mySQLDatabase.php";

$pdo = new mySQLDatabase();
$utility = new Utility();
$modelChange = new ModelChange();

$sql = "SELECT * FROM shoppingcart WHERE Opened = 1 AND CustomerOrder IS NULL AND deleted_at IS NULL";
$smtp = $pdo->executeStatement($sql);

if ($smtp) {
    $record = $smtp->fetchAll();
    if (count($record) > 0) {
        $scCastArray = array_values($modelChange->modelExchange($modelChange->modelRead('shoppingcart')));
        $array = [];
        foreach ($record as $row) {
            array_push($array, $modelChange->modelFillArray($scCastArray, $row));
        }
    }
    $json_response = json_encode($array);

    file_put_contents(PATH_OUTPUT."data.json", $json_response);

    $file = PATH_OUTPUT."data.json";

    $getFile = file_get_contents($file);

    $copy = copy( PATH_OUTPUT."data.json", PATH_OUTPUT."data.json" );
////    $c = curl_init();
////    curl_setopt($c, CURLOPT_URL, $urlPath);
////    curl_setopt($c, CURLOPT_USERPWD, "username:password");
////    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
////    curl_setopt($c, CURLOPT_PUT, true);
//////    curl_setopt($c, CURLOPT_INFILESIZE, filesize($getFile));
////
////    $fp = fopen($file, "r");
////    curl_setopt($c, CURLOPT_INFILE, $fp);
////
////    if ( curl_exec($c) === false ) {
////        echo curl_error($c) . "\n";
////    }
////
////    curl_close($c);
////    fclose($fp);

    echo $json_response;

} else {
    echo "Nincs új kosár!";
}

