<?php

require_once 'Database.php';
require_once 'Utility.php';

class ModelChange {

    public static $fillableStart = NULL;
    public static $fillableEnd = NULL;
    public static $castsStart = NULL;
    public static $castsEnd = NULL;
    public static $rulesStart = NULL;
    public static $rulesEnd = NULL;

    public static $fillableArray = [];
    public static $castsArray = [];
    public static $rulesArray = [];
    public static $elejeArray = [];
    public static $kozepArray = [];
    public static $kozep2Array = [];
    public static $vegeArray = [];

    public static $mentesArray = [];

    public static function init() {
        self::$fillableStart = NULL;
        self::$fillableEnd = NULL;
        self::$castsStart = NULL;
        self::$castsEnd = NULL;
        self::$rulesStart = NULL;
        self::$rulesEnd = NULL;

        self::$fillableArray = [];
        self::$castsArray = [];
        self::$rulesArray = [];
        self::$elejeArray = [];
        self::$kozepArray = [];
        self::$kozep2Array = [];
        self::$vegeArray = [];
        self::$mentesArray = [];
    }

    public static function mentesTombbe($mibe, $mi)
    {
        for ($i = 0; $i < count($mi); $i++)
        {
            array_push($mibe, $mi[$i]);
        }
        return $mibe;
    }


    public static function modelRead($item)
    {
        $path = dirname(__DIR__,2) . '/App/Models/';
        $item = $item != 'Lead' ? $item : "Leed";
        $fileName = $path. $item . '.php';
        if (file_exists($fileName)) {
            $current = file($fileName);
            return array_values($current);
        } else {
            echo $fileName . "\n";
        }

        return NUll;
    }

    public static function modelExchange($values)
    {
        self::getFilleable($values);
        self::arraysFill($values);

        return self::$castsArray;
    }

    public static function getFilleable($values)
    {
        self::init();
        for ($i = 0; $i < count($values); $i++) {
            if (strpos($values[$i], "fillable") > 0) {
                self::$fillableStart = is_null(self::$fillableStart) ? $i : self::$fillableStart;
            }
            if ((strpos($values[$i], "];") > 0) && (strlen(trim($values[$i])) === 2)) {
                if (is_null(self::$fillableEnd)) {
                    self::$fillableEnd = $i;
                } else {
                    if (($i > self::$fillableEnd) && is_null(self::$castsEnd)) {
                        self::$castsEnd = $i;
                    } else {
                        if (($i > self::$castsEnd) && is_null(self::$rulesEnd)) {
                            self::$rulesEnd = $i;
                        }
                    }
                }
            }
            if (strpos($values[$i],  "casts") > 0 )
            {
                self::$castsStart = is_null(self::$castsStart) ? $i : self::$castsStart;
            }
            if (strpos($values[$i],  "rules =") > 0 )
            {
                self::$rulesStart = is_null(self::$rulesStart) ? $i : self::$rulesStart;
            }

        }
    }

    public static function arraysFill($values)
    {
        for ($i = 0; $i <= count($values); $i++)
        {
            if ( $i <= self::$fillableStart ) {
                array_push(self::$elejeArray, $values[$i]);
            } elseif ( ($i > self::$fillableStart) && ($i < self::$fillableEnd)) {
                array_push(self::$fillableArray, $values[$i]);
            } elseif ( ($i >= self::$fillableEnd) && ($i <= self::$castsStart)) {
                array_push(self::$kozepArray, $values[$i]);
            } elseif ( ($i >= self::$castsStart) && ($i < self::$castsEnd)) {
                array_push(self::$castsArray, $values[$i]);
            } elseif ( ($i >= self::$castsEnd) && ($i <= self::$rulesStart)) {
                array_push(self::$kozep2Array, $values[$i]);
            } elseif ( ($i >= self::$rulesStart) && ($i < self::$rulesEnd)) {
                array_push(self::$rulesArray, $values[$i]);
            } elseif ( ($i >= self::$rulesEnd) && ($i < count($values))) {
                array_push(self::$vegeArray, $values[$i]);
            }
        }
    }

    public static function fieldArrayControll($fieldArray, $item)
    {
        for ( $i = 0; $i < count($fieldArray); $i++) {
            $fieldArrayValues = array_values($fieldArray[$i]);
            $field = $fieldArrayValues[0];
            $pos = 0;
            for ( $j = 0; $j < count(self::$castsArray); $j++) {
                $pos = strpos(self::$castsArray[$j], "'" . $field . "'");
                if ( $pos != false) {
                    break;
                }
            }
            if ( $pos === false ) {
                self::createNewField($item, $fieldArrayValues);
            }
        }

        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$elejeArray);
        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$fillableArray);
        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$kozepArray);
        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$castsArray);
        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$kozep2Array);
        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$rulesArray);
        self::$mentesArray = self::mentesTombbe(self::$mentesArray, self::$vegeArray);

        $fp = dirname(__DIR__,2) . '/App/Models/' . $item['table'] . '.php';
        file_put_contents($fp, self::$mentesArray);
    }

    public static function howMany($field)
    {
        $begin = strpos($field, ",");
        $end = strpos($field, ")");
        if ( $begin != false && $end != false) {
            $decimals = substr($field, $begin + 1, ($end - ($begin + 1)));
        }
        return isset($decimals) ? $decimals : null;
    }

    public static function fieldType($field)
    {
        if ( strpos($field, 'BLOB') != false || $field == 'BLOB') {
            return "string";
        }
        if ( strpos($field, 'CHAR') != false || $field == 'CHAR') {
            return "string";
        }
        if ( strpos($field, 'TIMESTAMP') != false || $field == 'TIMESTAMP') {
            return "datetime";
        }
        if ( strpos($field, 'NUMERIC') != false || $field == 'NUMERIC') {
            return "decimal";
        }
        if ( strpos($field, 'INT') != false || $field == 'INT') {
            return "integer";
        }
    }

    public static function createNewField($item, $fieldArrayValues)
    {
        $fieldEOL = "'" .$fieldArrayValues[0] . "'\n";
        $newFieldFillable = str_repeat(' ', strpos(self::$fillableArray[0], "'")) . $fieldEOL;
        echo $newFieldFillable . "\n";
        self::$fillableArray[count(self::$fillableArray) - 1] = substr(self::$fillableArray[count(self::$fillableArray) - 1], 0, iconv_strpos(self::$fillableArray[count(self::$fillableArray) - 1], "\n", 0)) . ','."\n";
        array_push(self::$fillableArray, $newFieldFillable);

        $type = self::fieldType($fieldArrayValues[1]);
        if (strpos($fieldArrayValues[1], 'NUMERIC') != false ) {
            $decimals = self::howMany($fieldArrayValues[1]);
        }

        $newFieldCasts = str_repeat(' ', strpos(self::$castsArray[0], "'")) . "'" .$fieldArrayValues[0]  . "' => " . "'" . ($type != "decimal" ? $type : $type . ":" . $decimals) . "'". "\n";
        self::$castsArray[count(self::$castsArray) - 1] = substr(self::$castsArray[count(self::$castsArray) - 1], 0, iconv_strpos(self::$castsArray[count(self::$castsArray) - 1], "\n", 0)) . ','."\n";
        array_push(self::$castsArray, $newFieldCasts);

        $smtp = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = LOWER( '" . $item['table']. "') AND TABLE_SCHEMA= '" . getenv('DB_DATABASE') ."'";
        $results = DB::run($smtp);

        $changing = true;
        foreach($results->fetchAll(PDO::FETCH_ASSOC) as $result) {
            $values = array_values($result);
            foreach ($values as $value) {
                if ( $value == $fieldArrayValues[0] ) {
                    $changing = false;
                }
            }
        }

        if ( $changing ) {
            $smtp = 'ALTER TABLE ' . $item['table'] . ' ADD';
            $smtp = $smtp. " ". $fieldArrayValues[0] . " " . $fieldArrayValues[1];
            DB::run($smtp);
        }
    }

    public static function modelFillArray($scCastArray, $row)
    {
        $string = NULL;
        for ( $i = 0; $i < count($scCastArray); $i++ ) {
            $field = ltrim(substr($scCastArray[$i], 0, strpos($scCastArray[$i], "=>") - 1));
            $value = $row[substr($field, 1, strlen($field) - 2)];
            if ( $i < count($scCastArray) - 1 ) {
                $string .= $field . " => " . $value . ",";
            } else {
                $string .= $field . " => " . $value;
            }
        };
        return array($string);

    }

}
