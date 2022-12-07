<?php
namespace App\Classes;

use DB;

Class xmlClass{

    public static function ciklus($keys, $values, $count)
    {
        for ($i = 0; $i < count($values); $i++) {
            if (gettype($values[$i]) == "array") {
                $keys10 = array_keys($values[$i]);
                $values10 = array_values($values[$i]);
                for ($s = 0; $s < count($values10); $s++) {
                    echo "Ez a 10 ". " ". $keys10[$s] . " ". $values10[$s] . "\n";
                }
                echo "\n";
            }
        }
    }
}
