<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 6000);

date_default_timezone_set("Europe/Budapest");

define("PROJECT_ROOT_PATH", dirname(__DIR__, 3));
define("PATH_INC", dirname(__DIR__, 2). '/Classes/Api/Inc');
define('PATH_OUTPUT', dirname(__DIR__, 4) . '/storage/output/');
define('PATH_INPUT', dirname(__DIR__, 4) . '/storage/input/');
define('PATH_MODELS', dirname(__DIR__, 3) . '/Models/');
