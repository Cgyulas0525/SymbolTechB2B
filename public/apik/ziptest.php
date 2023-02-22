<?php
require __DIR__ . "/inc/bootstrap.php";

$utility = new Utility();
$files = array_diff(preg_grep('~\.(zip)$~', scandir(PATH_XML)), array('.', '..'));

foreach ($files as $file) {
    $utility->unZip($file);
    $utility->fileUnlink(PATH_XML.$file);
}

$files = array_diff(preg_grep('~\.(xml)$~', scandir(PATH_XML)), array('.', '..'));
foreach ($files as $file) {
    echo $file;
}
