<?php

namespace App\Classes\Api;

use App\Classes\Api\apiUtilityClass;

class fileDeleteClass
{
    public $utility = NULL;

    function __construct() {
        require_once dirname(__DIR__, 2). "/Classes/Api/Inc/config.php";
        $this->utility = new apiUtilityClass();
    }

    public function deleteOutputFiles() {
        $files = $files = array_diff(preg_grep('~\.(txt)$~', scandir(PATH_OUTPUT)), array('.', '..'));
        foreach ($files as $file) {
            $this->utility->fileUnlink(PATH_OUTPUT.$file);
        }
    }
}
