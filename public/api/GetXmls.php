<?php

require_once 'Database.php';
require_once 'Utility.php';
require_once 'ModelChange.php';
require_once 'witchStrpos.php';
require_once 'MakeDateFormat.php';

class XML {
    public static $path = NULL;

    public static $castsKeys = [];
    public static $castsValues = [];

    public static function __constructor()
    {
        self::$path = dirname(__DIR__,2) . '/public/xml/';
    }

    /*
     * sql for new record insert
     *
     * @param $model - B2B model - table name
     * @param $keys - field names in the $model
     * @param $values - new record values
     *
     * @return string
     */
    public static function makeInsert($model, $keys, $values)
    {
        $smtpFieldEleje = 'INSERT INTO ' . $model . ' (';
        $smtpValueEleje = 'VALUES (';
        $smtpField = "";
        $smtpValue = "";
        for ( $i = 0; $i < count($keys); $i++ ){
            if ($keys[$i] != 'Lead') {
                if ($keys[$i] == 'Foreign') {
                    $tableFieldName = 'Foreignn';
                    $smtpField = $i == count($keys) - 1 ? $smtpField . 'Foreignn' . ") " : $smtpField . 'Foreignn' . ", ";
                } else {
                    $tableFieldName = $keys[$i];
                    $smtpField = $i == count($keys) - 1 ? $smtpField . $keys[$i] . ") " : $smtpField . $keys[$i] . ", ";
                }
            } else {
                $tableFieldName = 'LeadId';
                $smtpField = $i == count($keys) - 1 ? $smtpField . 'LeadId' . ") " : $smtpField . 'LeadId' . ", ";
            }
            for ( $j = 0; $j < count(self::$castsValues); $j++) {
                if (strpos(self::$castsValues[$j], $keys[$i])) {
                    $string = trim(preg_replace('/\s+/',' ', self::$castsValues[$j]));
                    $fieldName = witchStrpos::getSubstr($string, 1);
                    if ( $tableFieldName == $fieldName) {
                        $type = witchStrpos::getSubstr($string, 3);
                        switch ($type) {
                            case "integer" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $values[$i] . ")" : $smtpValue . $values[$i] . ", ";
                                break;
                            case "string" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . "'" . $values[$i] . "')" : $smtpValue . "'" . $values[$i] . "', ";
                                break;
                            case "datetime" :
                                $sqlDate = MakeDateFormat::makeSQLDate($values[$i]);
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $sqlDate . ")" : $smtpValue . $sqlDate . ", ";
                                break;
                            case "decimal:4" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $values[$i] . ")" : $smtpValue . $values[$i] . ", ";
                                break;
                            default :
                                echo "????? \n";
                        }
                        break;
                    }
                }
            }
        }
        return $smtpFieldEleje . $smtpField . $smtpValueEleje . $smtpValue;
    }

    /*
     * sql for record modify
     *
     * @param $model - B2B model - table name
     * @param $keys - field names in the $model
     * @param $values - new values
     *
     * @return string
     */
    public static function makeUpdate($model, $keys, $values, $id)
    {
        $smtpFieldEleje = "UPDATE " . $model . " SET ";
        $smtpWhere = "WHERE Id = '" .$id. "'" ;
        $smtpValue = "";
        for ( $i = 0; $i < count($keys); $i++ ){
            for ( $j = 0; $j < count(self::$castsValues); $j++) {
                if (strpos(self::$castsValues[$j], $keys[$i])) {
                    $string = trim(preg_replace('/\s+/',' ', self::$castsValues[$j]));
                    $fieldName = witchStrpos::getSubstr($string, 1);
                    $keyName = $keys[$i] == 'Lead' ? 'LeadId' : ($keys[$i] == 'Foreign' ? 'Foreignn' : $keys[$i]);
                    if ( $fieldName == $keyName) {
                        $type = witchStrpos::getSubstr($string, 3);
                        switch ($type) {
                            case "integer" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "=" . $values[$i] . " " : $smtpValue . " " . $keyName . "=" . $values[$i] . ", ";
                                break;
                            case "string" :
                                if (is_array($values[$i])) {
                                    if ( count(array_values($values[$i])) == 0) {
                                        $value = "";
                                    }
                                } else {
                                    $value = $values[$i];
                                }
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "= '" . $value . "' " : $smtpValue . " " . $keyName . "= '" . $value . "', ";
                                break;
                            case "datetime" :
                                $sqlDate = MakeDateFormat::makeSQLDate($values[$i]);
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "=" . $sqlDate . " " : $smtpValue . " " . $keyName . "=" . $sqlDate . ", ";
                                break;
                            case "decimal:4" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "=" . $values[$i] . " " : $smtpValue . " " . $keyName . "=" . $values[$i] . ", ";
                                break;
                            default :
                                echo "????? \n";
                        }
                        break;
                    }
                }
            }
        }
        return $smtpFieldEleje . $smtpValue . $smtpWhere;
    }

    /*
     * file loader and json processing
     *
     * @return void
     */
    public static function xmlLoader()
    {
        self::$path = dirname(__DIR__,2) . '/public/xml/';
        $files = array_diff(preg_grep('~\.(xml)$~', scandir(self::$path)), array('.', '..'));
        foreach ($files as $file) {
            $phpDataArray = Utility::fileLoader(self::$path . $file);
            for ($j = 0;$j < count($phpDataArray); $j++) {
                $model = array_keys($phpDataArray)[$j];
                if ($model != "PlugIn") {

                    if ( $model == 'Lead') {
                        $model = "Leed";
                    }

                    $modelArray = ModelChange::modelRead($model);
                    $castsArray = ModelChange::modelExchange($modelArray);
                    self::$castsKeys = array_keys($castsArray);
                    self::$castsValues = array_values($castsArray);

                    $count = $model == 'Leed' ? count($phpDataArray['Lead']) : count($phpDataArray[$model]);

                    if ($count > 0) {
                        foreach ($phpDataArray[$model == 'Leed' ? 'Lead' : $model] as $index => $data) {
                            if (!is_array($data)) {
                                $keys = array_keys($phpDataArray[$model == 'Leed' ? 'Lead' : $model]);
                                $values = array_values($phpDataArray[$model == 'Leed' ? 'Lead' : $model]);
                            } else {
                                $keys = array_keys($data);
                                $values = array_values($data);
                            }
                            $sql = "SELECT Count(*) as db FROM " . $model . " WHERE Id = '" . $values[array_search('Id', $keys)] . "'";
                            $smtp = DB::run($sql);
                            if ($smtp) {
                                $record = $smtp->fetchAll();
                                if (count($record) > 0) {
                                    foreach ($record as $row) {
                                        if ( intval($row['db']) === 1 ) {
                                            $smtp = self::makeUpdate($model, $keys, $values, $values[array_search('Id', $keys)]);
                                        } else {
                                            $smtp = self::makeInsert($model, $keys, $values);
                                        }
                                    }
                                }
                            } else {
                                return $sql . ' hibával tért vissza!';
                            }
                            DB::run($smtp);
                        }
                    }
                }
            }
            Utility::fileUnlink(self::$path.$file);
        }
        Utility::httpPost(self::$path, "OK");
    }
}



