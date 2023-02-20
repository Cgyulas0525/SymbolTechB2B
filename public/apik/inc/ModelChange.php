<?php

class ModelChange {

    public $fillableStart = NULL;
    public $fillableEnd = NULL;
    public $castsStart = NULL;
    public $castsEnd = NULL;
    public $rulesStart = NULL;
    public $rulesEnd = NULL;

    public $fillableArray = [];
    public $castsArray = [];
    public $rulesArray = [];
    public $frontArray = [];
    public $midleArray = [];
    public $midle2Array = [];
    public $endArray = [];

    public $saveArray = [];

    public $utility = NULL;
    public $pdo = NULL;

    function __construct() {
        date_default_timezone_set("Europe/Budapest");
        $this->utility = new Utility();
        $this->pdo = new mySQLDatabase();
    }


    public function init() {
        $this->fillableStart = NULL;
        $this->fillableEnd = NULL;
        $this->castsStart = NULL;
        $this->castsEnd = NULL;
        $this->rulesStart = NULL;
        $this->rulesEnd = NULL;
        $this->fillableArray = [];
        $this->castsArray = [];
        $this->rulesArray = [];
        $this->frontArray = [];
        $this->midleArray = [];
        $this->midle2Array = [];
        $this->endArray = [];
        $this->saveArray = [];
    }

    /*
     * Save from $witchArray to $array
     */
    public function saveToArray($array, $witchArray) {
        for ($i = 0; $i < count($witchArray); $i++) {
            array_push($array, $witchArray[$i]);
        }
        return $array;
    }


    /*
     * Model read from App\Models
     */
    public function modelRead($item) {
        $item = $item != 'Lead' ? $item : "Leed";
        $fileName = PATH_MODELS. $item . '.php';
        if (file_exists($fileName)) {
            $current = file($fileName);
            return array_values($current);
        } else {
            echo $fileName . "\n";
        }

        return NUll;
    }

    /*
     * Model exchange
     */
    public function modelExchange($values) {
        $this->getFilleable($values);
        $this->arraysFill($values);

        return $this->castsArray;
    }

    public function getFilleable($values) {
        $this->init();
        for ($i = 0; $i < count($values); $i++) {
            if (strpos($values[$i], "fillable") > 0) {
                $this->fillableStart = is_null($this->fillableStart) ? $i : $this->fillableStart;
            }
            if ((strpos($values[$i], "];") > 0) && (strlen(trim($values[$i])) === 2)) {
                if (is_null($this->fillableEnd)) {
                    $this->fillableEnd = $i;
                } else {
                    if (($i > $this->fillableEnd) && is_null($this->castsEnd)) {
                        $this->castsEnd = $i;
                    } else {
                        if (($i > $this->castsEnd) && is_null($this->rulesEnd)) {
                            $this->rulesEnd = $i;
                        }
                    }
                }
            }
            if (strpos($values[$i],  "casts") > 0 )
            {
                $this->castsStart = is_null($this->castsStart) ? $i : $this->castsStart;
            }
            if (strpos($values[$i],  "rules =") > 0 )
            {
                $this->rulesStart = is_null($this->rulesStart) ? $i : $this->rulesStart;
            }
        }
    }

    /*
     * Object arrays fill from Models arrays
     */
    public function arraysFill($values) {
        for ($i = 0; $i <= count($values); $i++)
        {
            if ( $i <= $this->fillableStart ) {
                array_push($this->frontArray, $values[$i]);
            } elseif ( ($i > $this->fillableStart) && ($i < $this->fillableEnd)) {
                array_push($this->fillableArray, $values[$i]);
            } elseif ( ($i >= $this->fillableEnd) && ($i <= $this->castsStart)) {
                array_push($this->midleArray, $values[$i]);
            } elseif ( ($i >= $this->castsStart) && ($i < $this->castsEnd)) {
                array_push($this->castsArray, $values[$i]);
            } elseif ( ($i >= $this->castsEnd) && ($i <= $this->rulesStart)) {
                array_push($this->midle2Array, $values[$i]);
            } elseif ( ($i >= $this->rulesStart) && ($i < $this->rulesEnd)) {
                array_push($this->rulesArray, $values[$i]);
            } elseif ( ($i >= $this->rulesEnd) && ($i < count($values))) {
                array_push($this->endArray, $values[$i]);
            }
        }
    }

    /*
     * Model arrays save
     */
    public function fieldArrayControll($fieldArray, $item) {
        for ( $i = 0; $i < count($fieldArray); $i++) {
            $fieldArrayValues = array_values($fieldArray[$i]);
            $field = $fieldArrayValues[0];
            $pos = 0;
            for ( $j = 0; $j < count($this->castsArray); $j++) {
                $pos = strpos($this->castsArray[$j], "'" . $field . "'");
                if ( $pos != false) {
                    break;
                }
            }
            if ( $pos === false ) {
                $this->createNewField($item, $fieldArrayValues);
            }
        }

        $this->saveArray = $this->saveToArray($this->saveArray, $this->frontArray);
        $this->saveArray = $this->saveToArray($this->saveArray, $this->fillableArray);
        $this->saveArray = $this->saveToArray($this->saveArray, $this->midleArray);
        $this->saveArray = $this->saveToArray($this->saveArray, $this->castsArray);
        $this->saveArray = $this->saveToArray($this->saveArray, $this->midle2Array);
        $this->saveArray = $this->saveToArray($this->saveArray, $this->rulesArray);
        $this->saveArray = $this->saveToArray($this->saveArray, $this->endArray);

        $fp = PATH_MODELS . $item['table'] . '.php';
        file_put_contents($fp, $this->saveArray);
    }

    public function howMany($field) {
        $begin = strpos($field, ",");
        $end = strpos($field, ")");
        if ( $begin != false && $end != false) {
            $decimals = substr($field, $begin + 1, ($end - ($begin + 1)));
        }
        return isset($decimals) ? $decimals : null;
    }

    /*
     * Field type identification
     */
    public function fieldType($field) {
        if ( strpos($field, 'BLOB') !== false || $field == 'BLOB') {
            return "string";
        }
        if ( strpos($field, 'CHAR') !== false || $field == 'CHAR') {
            return "string";
        }
        if ( strpos($field, 'TIMESTAMP') !== false || $field == 'TIMESTAMP') {
            return "datetime";
        }
        if ( strpos($field, 'NUMERIC') !== false || $field == 'NUMERIC') {
            return "decimal";
        }
        if ( strpos($field, 'INT') !== false || $field == 'INT') {
            return "integer";
        }
    }

    /*
     * Create new field in database
     */
    public function createNewField($item, $fieldArrayValues) {
        $fieldEOL = "'" .$fieldArrayValues[0] . "'\n";
        $newFieldFillable = str_repeat(' ', strpos($this->fillableArray[0], "'")) . $fieldEOL;
        $this->fillableArray[count($this->fillableArray) - 1] = substr($this->fillableArray[count($this->fillableArray) - 1], 0, iconv_strpos($this->fillableArray[count($this->fillableArray) - 1], "\n", 0)) . ','."\n";
        array_push($this->fillableArray, $newFieldFillable);

        if (strpos($fieldArrayValues[1], 'BLOB') !== false ) {
            $fieldArrayValues[1] = "BLOB";
        }

        $type = $this->fieldType($fieldArrayValues[1]);
        if (strpos($fieldArrayValues[1], 'NUMERIC') !== false ) {
            $decimals = $this->howMany($fieldArrayValues[1]);
        }

        $newFieldCasts = str_repeat(' ', strpos($this->castsArray[0], "'")) . "'" .$fieldArrayValues[0]  . "' => " . "'" . ($type != "decimal" ? $type : $type . ":" . $decimals) . "'". "\n";
        $this->castsArray[count($this->castsArray) - 1] = substr($this->castsArray[count($this->castsArray) - 1], 0, iconv_strpos($this->castsArray[count($this->castsArray) - 1], "\n", 0)) . ','."\n";
        array_push($this->castsArray, $newFieldCasts);

        $smtp = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = LOWER( '" . $item['table']. "') AND TABLE_SCHEMA= '" . getenv('DB_DATABASE') ."'";
        $results = $this->pdo->executeStatement($smtp);

        $changing = true;
        foreach($results->fetchAll(PDO::FETCH_ASSOC) as $result) {
            $values = array_values($result);
            foreach ($values as $value) {
                if ( $value == $fieldArrayValues[0] ) {
                    $changing = false;
                    break;
                }
            }
        }

        if ( $changing ) {
            $smtp = 'ALTER TABLE ' . $item['table'] . ' ADD';
            $smtp = $smtp. " ". $fieldArrayValues[0] . " " . $fieldArrayValues[1];
            $this->pdo->executeStatement($smtp);
        }
    }

    /*
     * Model $fillable array modify
     */
    public static function modelFillArray($scCastArray, $row) {
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
