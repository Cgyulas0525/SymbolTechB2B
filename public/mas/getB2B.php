<?php
require __DIR__ . "/inc/bootstrap.php";
require PATH_FILES . "/getB2B.php";
require PATH_MODEL . '/api.php';
require PATH_INC . "/utility.php";

$getB2B = new getB2B();
$api = new api();
$utility = new Utility();

$files = array_diff(preg_grep('~\.(xml)$~', scandir(PATH_XML)), array('.', '..'));

foreach ($files as $file) {
    echo $file . "\n";
    echo PATH_XML.$file."\n";
    $api->insert($file);
    $getB2B->getFile($file, $api);

    $utility->fileUnlink(PATH_XML.$file);
}

$utility->httpPost(PATH_XML, "OK");
