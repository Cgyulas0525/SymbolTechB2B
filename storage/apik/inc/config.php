<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 6000);

define('PATH_XML', dirname(__DIR__, 2) . '/xml/');
define('PATH_MODEL', dirname(__DIR__, 2) . '/apik/model');
define('PATH_INC', dirname(__DIR__, 2) . '/apik/inc');
define('PATH_FILES', dirname(__DIR__, 2) . '/apik/files');
define('PATH_MODELS', dirname(__DIR__, 3) . '/app/Models/');
define('PATH_OUTPUT', dirname(__DIR__, 2) . '/output/');

require PATH_INC . '/utility.php';
$utility = new Utility();

define('MYSQL_HOST', "localhost");
define('MYSQL_DATABASE', "b2b");
define('MYSQL_USERNAME', "root");
define('MYSQL_PASSWORD', "PASSWORD");
define('MYSQL_CHARSET', "utf8");

define('CURRENCYRATE_URL', 'http://api.napiarfolyam.hu?bank=');


define('VALIDFROM', date('Y-m-d H:i:s', strtotime('midnight')));
define('DATE_NOW', date('Y-m-d H:i:s', strtotime('now')));


