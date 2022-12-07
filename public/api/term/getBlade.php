<?php

require_once 'bladeClass.php';

bladeClass::start();
bladeClass::getFolder(bladeClass::$path);

foreach (bladeClass::$fileArray as $f) {
    $file = file($f);
    foreach ($file as $line) {
        bladeClass::lineProcessing($line);
    }
};

foreach (bladeClass::$textArray as $text) {
    echo $text . "\n";
}

