<?php

require dirname(__DIR__, 1) . "/apik/inc/bootstrap.php";
require PATH_INC . "/ModelChange.php";
require PATH_FILES . "/GetXsd.php";
require PATH_INC. "/curlPost.php";
require PATH_MODEL . "/mySQLDatabase.php";

$utility = new Utility();
$xsd = new XSD();
$modelChange = new ModelChange();

$files = array_diff(preg_grep('~\.(xsd)$~', scandir(PATH_XML)), array('.', '..'));
foreach ( $files as $file ) {
    $xmlFile = substr( $file, 0, strpos( $file, '.xsd' )) . "mas.xml";
    $array = $xsd->getXSD(substr( $file, 0, strpos( $file, '.xsd' )));

    foreach ($array as $item) {
        $fieldArray = array_values($item['value']);

        $modelArray = $modelChange->modelRead($item['table']);
        if (is_array($modelArray)) {
            $castsArray = $modelChange->modelExchange($modelArray);
            if ( count($castsArray) != count($fieldArray)) {
                // ha van új mező
                if (count($castsArray) < count($fieldArray)) {
                    $modelChange->fieldArrayControll($fieldArray, $item);
                }
                // ha kikerült mező a táblából
                if (count($castsArray) > count($fieldArray)) {
                    echo $item['table'] . " ". count($castsArray) . " " . count($fieldArray) . "\n";
                }
            }
        }
    }
    $utility->fileUnlink(PATH_XML.$file);
    $utility->fileUnlink(PATH_XML.$xmlFile);
}

//$utility->httpPost(PATH_XML, "OK");

$curl = new CurlPost('http://localhost/Laravel/SymbolB2B/public/xml');

try {
    // execute the request
    echo $curl([
        'ok' => 'OK',
    ]);
} catch (\RuntimeException $ex) {
    // catch errors
    die(sprintf('Http error %s with code %d', $ex->getMessage(), $ex->getCode()));
}

