<?php

class modifyRekordB2B
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
        $smtpValue = "";
        for ( $i = 0; $i < count($keys); $i++ ){
            for ( $j = 0; $j < count($castsValues); $j++) {
                if (strpos($castsValues[$j], $keys[$i])) {
                    $string = trim(preg_replace('/\s+/',' ', $castsValues[$j]));
                    $fieldName = $this->witchStrpos->getSubstr($string, 1);
                    $keyName = $keys[$i] == 'Lead' ? 'LeadId' : ($keys[$i] == 'Foreign' ? 'Foreignn' : $keys[$i]);
                    if ( $fieldName == $keyName) {
                        $type = $this->witchStrpos->getSubstr($string, 3);
                        switch ($type) {
                            case "integer" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "=" . $values[$i] . " " : $smtpValue . " " . $keyName . "=" . $values[$i] . ", ";
                                break;
                            case "string" :
                                if (is_array($values[$i])) {
                                    if ( count(array_values($values[$i])) == 0) {
                                        $value = null;
                                    }
                                } else {
                                    $value = $values[$i];
                                }
                                $pos = strpos($value , '"');
                                if (strpos($value, '"') != false) {
                                    $value = str_replace('"', "'", $value);
                                }

//                                if (strpos($value, 'xml version') === false &&
//                                    strpos($value, "'") === false &&
//                                    strpos($value, '{') === false &&
//                                    strpos($value, 'style="MARGIN') === false ) {
                                    $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "= '" . $value . "' " : $smtpValue . " " . $keyName . "= '" . $value . "', ";
//                                }
                                break;
                            case "datetime" :
                                $sqlDate = $this->makeDateFormat->makeSQLDate($values[$i]);
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
        return "UPDATE " . $model . " SET " . $smtpValue . "WHERE Id = '" . $values[0] . "'";
    }
}
