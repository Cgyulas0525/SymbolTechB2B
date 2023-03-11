<?php

namespace App\Classes\Api;

use App\Classes\Api\apiUtilityClass;
use DB;
use Illuminate\Support\Facades\Schema;

class truncateTablesClass
{

    private $utility = NULL;
    private $outputFile = NULL;

    function __construct()
    {
        require_once dirname(__DIR__, 2) . "/Classes/Api/Inc/config.php";
        $this->utility = new apiUtilityClass();

        $this->outputFile = fopen(PATH_OUTPUT . 'truncate-'. uniqid() . '.txt', "w") or die("Unable to open file!");
        $this->utility->fileWrite($this->outputFile, "B2B getCurrency\n");
        $this->utility->fileWrite($this->outputFile, "Start: " . date('Y.m.d H:m:s', strtotime('now')) . "\n");
    }

    public function truncateTables() {

        $envName = env('DB_DATABASE');
        $dbName = "Tables_in_". $envName;
        $tables = Schema::getAllTables();
        foreach ($tables as $table) {
            if ($table->$dbName != 'dictionaries' && $table->$dbName != 'languages' && $table->$dbName != 'users') {
                Schema::disableForeignKeyConstraints();
                DB::table($table->$dbName)->truncate();
                $this->utility->fileWrite($this->outputFile, "Truncate: " . $table->$dbName . "\n");
                Schema::enableForeignKeyConstraints();
            }
        }
        $this->utility->fileWrite($this->outputFile, "OK\n");
        $this->utility->fileWrite($this->outputFile, "End: " . date('Y.m.d H:m:s', strtotime('now')) . "\n");
        fclose($this->outputFile);

    }

}
