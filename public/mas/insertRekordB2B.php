<?php

class insertRekordB2B
{
    public $witchStrpos;
    public $makeDateFormat;

    function __construct() {
        $this->witchStrpos = new witchStrpos();
        $this->makeDateFormat = new makeDateFormat();
    }

    /*
     * sql text készítés
     *
     * @param $model - string - a model neve
     * @param $keys - array - kulcsok
     * @param $values - array - értékek
     * @param $castsValues - array - model mező leíró tömb
     *
     * @return string
     */
    public function makeSmtp($model, $keys, $values, $castsValues) {
        $smtpField = "";
        $smtpValue = "";
        for ( $i = 0; $i < count($keys); $i++ ){
            $keyName = $keys[$i] == 'Lead' ? 'LeadId' : ($keys[$i] == 'Foreign' ? 'Foreignn' : $keys[$i]);
            for ( $j = 0; $j < count($castsValues); $j++) {
                if (strpos($castsValues[$j], $keys[$i])) {
                    $string = trim(preg_replace('/\s+/',' ', $castsValues[$j]));
                    $fieldName = $this->witchStrpos->getSubstr($string, 1);
                    if ( $keyName == $fieldName) {
                        $type = $this->witchStrpos->getSubstr($string, 3);
                        switch ($type) {
                            case "integer" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $values[$i] . ")" : $smtpValue . $values[$i] . ", ";
                                $smtpField = $i == count($keys) - 1 ? $smtpField . $keyName . ") " : $smtpField . $keyName . ", ";
                                break;
                            case "string" :
                                if (is_array($values[$i])) {
                                    if ( count(array_values($values[$i])) == 0) {
                                        $value = null;
                                    }
                                } else {
                                    $value = $values[$i];
                                }
                                if (strpos($value, 'xml version') === false &&
                                    strpos($value, '{') === false &&
                                    strpos($value, 'style="MARGIN') === false) {
                                    $smtpValue = $i == count($keys) - 1 ? $smtpValue . "'" . $value . "')" : $smtpValue . "'" . $value . "', ";
                                    $smtpField = $i == count($keys) - 1 ? $smtpField . $keyName . ") " : $smtpField . $keyName . ", ";
                                }
                                break;
                            case "datetime" :
                                $sqlDate = $this->makeDateFormat->makeSQLDate($values[$i]);
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $sqlDate . ")" : $smtpValue . $sqlDate . ", ";
                                $smtpField = $i == count($keys) - 1 ? $smtpField . $keyName . ") " : $smtpField . $keyName . ", ";
                                break;
                            case "decimal:4" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $values[$i] . ")" : $smtpValue . $values[$i] . ", ";
                                $smtpField = $i == count($keys) - 1 ? $smtpField . $keyName . ") " : $smtpField . $keyName . ", ";
                                break;
                            default :
                                echo "????? \n";
                        }
                        break;
                    }
                }
            }
        }
        return "INSERT INTO " . $model . " (" . $smtpField . " VALUES (" . $smtpValue;
    }
}
