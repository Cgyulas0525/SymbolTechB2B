<?php

require dirname(__DIR__, 1) . "/apik/inc/bootstrap.php";
require PATH_INC . "/ModelChange.php";
require PATH_FILES . "/GetXsd.php";
require PATH_INC. "/curlPost.php";
require PATH_MODEL . "/mySQLDatabase.php";

date_default_timezone_set("Europe/Budapest");

$utility = new Utility();
$xsd = new XSD();
$modelChange = new ModelChangeClass();

$outputFile = fopen(PATH_OUTPUT . 'getXSD-' . uniqid() . '.txt', "w") or die("Unable to open file!");
$txt = "B2B getXSD\n";
fwrite($outputFile, $txt);
$txt = "Start: " . date('Y.m.d h:m:s', strtotime('now')) . "\n";
fwrite($outputFile, $txt);

$files = array_diff(preg_grep('~\.(xsd)$~', scandir(PATH_XML)), array('.', '..'));
if (count($files) > 0) {
    foreach ($files as $file) {
        $txt = "File: " . $file . "\n";
        fwrite($outputFile, $txt);

        $xmlFile = substr($file, 0, strpos($file, '.xsd')) . "mas.xml";
        $array = $xsd->getXSD(substr($file, 0, strpos($file, '.xsd')));

        foreach ($array as $item) {
            $fieldArray = array_values($item['value']);

            $modelArray = $modelChange->modelRead($item['table']);
            if (is_array($modelArray)) {
                $castsArray = $modelChange->modelExchange($modelArray);
                if (count($castsArray) != count($fieldArray)) {
                    // ha van új mező
                    if (count($castsArray) < count($fieldArray)) {
                        $modelChange->fieldArrayControll($fieldArray, $item);
                    }
                    // ha kikerült mező a táblából
                    if (count($castsArray) > count($fieldArray)) {
                        $txt = $item['table'] . " " . count($castsArray) . " " . count($fieldArray) . "\n";;
                        fwrite($outputFile, $txt);
                    }
                }
            }
        }
        $utility->fileUnlink(PATH_XML . $file);
        $utility->fileUnlink(PATH_XML . $xmlFile);
    }
}
if ( count($files) == 0 )  {
    $txt = "Nem található feldogozandó file!\n";
    fwrite($outputFile, $txt);
}


$txt = "End: " . date('Y.m.d h:m:s', strtotime('now')) . "\n";
fwrite($outputFile, $txt);
fclose($outputFile);


