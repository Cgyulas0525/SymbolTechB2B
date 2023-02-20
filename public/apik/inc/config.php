<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 6000);

define('PATH_XML', dirname(__DIR__, 2) . '/xml/');
define('PATH_MODEL', dirname(__DIR__, 2) . '/apik/model');
define('PATH_INC', dirname(__DIR__, 2) . '/apik/inc');
define('PATH_FILES', dirname(__DIR__, 2) . '/apik/files');
define('PATH_MODELS', dirname(__DIR__, 3) . '/app/Models/');
define('PATH_OUTPUT', dirname(__DIR__, 2) . '/output/');

echo PATH_INC;

require PATH_INC . '/utility.php';
$utility = new Utility();

define('MYSQL_HOST', $utility->envLoader('DB_HOST'));
define('MYSQL_DATABASE', $utility->envLoader('DB_DATABASE'));
define('MYSQL_USERNAME', $utility->envLoader('DB_USERNAME'));
define('MYSQL_PASSWORD', $utility->envLoader('DB_PASSWORD'));
define('MYSQL_CHARSET', $utility->envLoader('DB_CHARSET'));

define('CURRENCYRATE_URL', 'http://api.napiarfolyam.hu?bank=');


define('VALIDFROM', date('Y-m-d H:i:s', strtotime('midnight')));
define('DATE_NOW', date('Y-m-d H:i:s', strtotime('now')));


