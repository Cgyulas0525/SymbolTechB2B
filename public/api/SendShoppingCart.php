<?php

require_once "Database.php";
require_once "ModelChange.php";
require_once "Utility.php";

$sql = "SELECT * FROM shoppingcart WHERE Opened = 1 AND CustomerOrder IS NULL AND deleted_at IS NULL AND Customer = " . $_GET['Customer'];
$smtp = DB::run($sql);

if ($smtp) {
    $record = $smtp->fetchAll();
    if (count($record) > 0) {
        $scCastArray = array_values(ModelChange::modelExchange(ModelChange::modelRead('shoppingcart')));
        $array = [];
        foreach ($record as $row) {
            array_push($array, ModelChange::modelFillArray($scCastArray, $row));
        }
    }
    $json_response = json_encode($array);

    $path = dirname(__DIR__,2) . '/public/output/';

    file_put_contents($path."data.json", $json_response);

    $file = $path."data.json";

    $getFile = file_get_contents($file);

    $copy = copy( $path."data.json", $path."data.json" );
//    $c = curl_init();
//    curl_setopt($c, CURLOPT_URL, $urlPath);
//    curl_setopt($c, CURLOPT_USERPWD, "username:password");
//    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($c, CURLOPT_PUT, true);
////    curl_setopt($c, CURLOPT_INFILESIZE, filesize($getFile));
//
//    $fp = fopen($file, "r");
//    curl_setopt($c, CURLOPT_INFILE, $fp);
//
//    if ( curl_exec($c) === false ) {
//        echo curl_error($c) . "\n";
//    }
//
//    curl_close($c);
//    fclose($fp);

    echo $json_response;

} else {
    echo "Nincs új kosár!";
}

