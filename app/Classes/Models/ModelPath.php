<?php

namespace App\Classes\Models;

class ModelPath
{

    public static function makeModelPath($table) {
        return 'App\Models\\'.$table;
    }

}
