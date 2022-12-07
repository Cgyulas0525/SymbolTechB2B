<?php

require_once 'GetXsd.php';
require_once 'ModelChange.php';
require_once 'Utility.php';

$path = dirname(__DIR__,2) . '/public/xml/';
$files = array_diff(preg_grep('~\.(xsd)$~', scandir($path)), array('.', '..'));
foreach ( $files as $file ) {
    $xmlFile = substr( $file, 0, strpos( $file, '.xsd' )) . "mas.xml";
    $array = XSD::getXSD(substr( $file, 0, strpos( $file, '.xsd' )));

    foreach ($array as $item) {
        $fieldArray = array_values($item['value']);

        $modelArray = ModelChange::modelRead($item['table']);
        if (is_array($modelArray)) {
            $castsArray = ModelChange::modelExchange($modelArray);
            if ( count($castsArray) != count($fieldArray)) {
                // ha van új mező
                if (count($castsArray) < count($fieldArray)) {
                    ModelChange::fieldArrayControll($fieldArray, $item);
                }
                // ha kikerült mező a táblából
                if (count($castsArray) > count($fieldArray)) {
                    echo $item['table'] . " ". count($castsArray) . " " . count($fieldArray) . "\n";
                }
            }
        }
    }
    Utility::fileUnlink($path.$file);
    Utility::fileUnlink($path.$xmlFile);
}

Utility::httpPost($path, "OK");

