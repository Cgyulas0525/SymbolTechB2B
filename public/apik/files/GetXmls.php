<?php
require PATH_MODEL . "/mySQLDatabase.php";
require PATH_INC . "/ModelChange.php";
require PATH_INC . "/witchStrpos.php";
require PATH_INC . "/MakeDateFormat.php";
require PATH_MODEL .'/api.php';
require PATH_MODEL . "/apimodel.php";

class XML {

    public $castsKeys = [];
    public $castsValues = [];
    public $pdo = NULL;
    public $utility = NULL;
    public $modelChange = NULL;
    public $witchStrpos = NULL;
    public $makeDateFormat = NULL;
    public $api = NULL;
    public $apimodel = NULL;
    public $outputFile = NULL;

    function __construct()
    {
        date_default_timezone_set("Europe/Budapest");
        $this->utility = new Utility();
        $this->pdo = new mySQLDatabase();
        $this->witchStrpos = new witchStrpos();
        $this->modelChange = new ModelChangeClass();
        $this->makeDateFormat = new MakeDateFormat();
        $this->api = new api();
        $this->apimodel = new apimodel();

        $this->outputFile = fopen(PATH_OUTPUT . 'getXML-' . uniqid() . '.txt', "w") or die("Unable to open file!");
        $txt = "B2B getXSD\n";
        fwrite($this->outputFile, $txt);
        $txt = "Start: " . date('Y.m.d h:m:s', strtotime('now')) . "\n";
        fwrite($this->outputFile, $txt);

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
    public function makeInsert($model, $keys, $values)
    {
        $smtpFieldBegin = 'INSERT INTO ' . $model . ' (';
        $smtpValueBegin = 'VALUES (';
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


            for ( $j = 0; $j < count($this->castsValues); $j++) {
                if (strpos($this->castsValues[$j], $keys[$i])) {
                    $string = trim(preg_replace('/\s+/',' ', $this->castsValues[$j]));
                    $fieldName = $this->witchStrpos->getSubstr($string, 1);
                    if ( $tableFieldName == $fieldName) {
                        if ( $tableFieldName == "CompanyType" && $model == "CustomerAddress") {
                            $vmi = $tableFieldName;
                        }
                        $type = $this->witchStrpos->getSubstr($string, 3);
                        switch ($type) {
                            case "integer" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $values[$i] . ")" : $smtpValue . $values[$i] . ", ";
                                break;
                            case "string" :
                                if (!is_string($values[$i])) {
                                    $values[$i] = '';
                                }
                                $value[$i] = strpos($values[$i], "'") != false ? str_replace("'", " ", $values[$i]) : $values[$i];
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . "'" . $values[$i] . "')" : $smtpValue . "'" . $values[$i] . "', ";
                                break;
                            case "datetime" :
                                $sqlDate = $this->makeDateFormat->makeSQLDate($values[$i]);
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $sqlDate . ")" : $smtpValue . $sqlDate . ", ";
                                break;
                            case "decimal:4" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . $values[$i] . ")" : $smtpValue . $values[$i] . ", ";
                                break;
                            default :
                                $txt = $fieldName . " mező típusa nem értelmezhető a " . $model . "táblában! \n";
                                fwrite($this->outputFile, $txt);
                        }
                        break;
                    }
                }
            }
        }
        $this->apimodel->insertednumber++;
        return $smtpFieldBegin . $smtpField . $smtpValueBegin . $smtpValue;
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
    public function makeUpdate($model, $keys, $values, $id)
    {
        $smtpFieldBegin = "UPDATE " . $model . " SET ";
        $smtpWhere = "WHERE Id = '" .$id. "'" ;
        $smtpValue = "";
        for ( $i = 0; $i < count($keys); $i++ ){
            for ( $j = 0; $j < count($this->castsValues); $j++) {
                if (strpos($this->castsValues[$j], $keys[$i])) {
                    $string = trim(preg_replace('/\s+/',' ', $this->castsValues[$j]));
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
                                        $value = "";
                                    }
                                } else {
                                    $value = strpos($values[$i], "'") != false ? str_replace("'", " ", $values[$i]) : $values[$i];
                                }
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "= '" . $value . "' " : $smtpValue . " " . $keyName . "= '" . $value . "', ";
                                break;
                            case "datetime" :
                                $sqlDate = $this->makeDateFormat->makeSQLDate($values[$i]);
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "=" . $sqlDate . " " : $smtpValue . " " . $keyName . "=" . $sqlDate . ", ";
                                break;
                            case "decimal:4" :
                                $smtpValue = $i == count($keys) - 1 ? $smtpValue . " " . $keyName . "=" . $values[$i] . " " : $smtpValue . " " . $keyName . "=" . $values[$i] . ", ";
                                break;
                            default :
                                $txt = $fieldName . " mező típusa nem értelmezhető a " . $model . "táblában! \n";
                                fwrite($this->outputFile, $txt);
//                                echo "????? \n";
                        }
                        break;
                    }
                }
            }
        }
        $this->apimodel->updatednumber++;
        return $smtpFieldBegin . $smtpValue . $smtpWhere;
    }

    public function apimodelerrorInsert($smtp, $return) {
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
        $smtp = 'INSERT INTO apimodelerror ( apimodel_id, smtp, error, created_at)
                 VALUES (' . $this->apimodel->id . ',' . $smtp . ',' . $return . ', DATE_FORMAT("' . $this->date . '", "%Y-%m-%d %H:%i:%s"))';
        $this->pdo->executeStatement($smtp);
    }

    /**
     * truncate database tables
     */
    public function truncateTables() {
        $vmi = $this->utility->envLoader('INSTALL_STATUS');
        if ($this->utility->envLoader('INSTALL_STATUS') === "1") {
            $tables = $this->pdo->tablesName();

            foreach ($tables as $table) {
                $value = array_values($table);
                if ($value[0] != 'dictionaries' && $value[0] != 'languages' && $value[0] != 'users') {
                    $sql = "DELETE FROM " . $value[0];
                    $this->pdo->executeStatement($sql);
                }
            }
        }
    }

    public function unzipFile() {
        $files = array_diff(preg_grep('~\.(zip)$~', scandir(PATH_XML)), array('.', '..'));

        if (count($files) > 0) {
            foreach ($files as $file) {
                $txt = "ZIP file: " . $file . "\n";
                fwrite($this->outputFile, $txt);

                $this->utility->unZip($file);
                $this->utility->fileUnlink(PATH_XML.$file);
            }
        }
        if (count($files) == 0) {
            $txt = "Nem található kicsomagolandó file!\n";
            fwrite($this->outputFile, $txt);
        }
    }

    /*
     * file loader and json processing
     *
     * @return void
     */
    public function xmlLoader()
    {
//        $this->truncateTables();
        $this->unzipFile();
        $files = array_diff(preg_grep('~\.(xml)$~', scandir(PATH_XML)), array('.', '..'));
        if (count($files) == 0) {
            $txt = "Nem található feldolgozandó file!\n";
            fwrite($this->outputFile, $txt);
        }
        if (count($files) > 0) {
            foreach ($files as $file) {
                $txt = "XML file: " . $file . "\n";
                fwrite($this->outputFile, $txt);

                $this->api->insert($file);
                $phpDataArray = $this->utility->fileLoader(PATH_XML . $file);
                for ($j = 0; $j < count($phpDataArray); $j++) {
                    $model = array_keys($phpDataArray)[$j];
                    if ($model != "PlugIn") {

                        if ( $model == 'Lead') {
                            $model = "Leed";
                        }

                        $modelArray = $this->modelChange->modelRead($model);
                        if (!is_null($modelArray)) {
                            $castsArray = $this->modelChange->modelExchange($modelArray);
                            $this->castsKeys = array_keys($castsArray);
                            $this->castsValues = array_values($castsArray);

                            $count = $model == 'Leed' ? count($phpDataArray['Lead']) : count($phpDataArray[$model]);

                            // insert apimodel
                            $this->apimodel->api_id = $this->api->id;
                            $this->apimodel->model = $model;
                            $this->apimodel->recordnumber = $count;
                            $this->apimodel->insertednumber = 0;
                            $this->apimodel->updatednumber = 0;
                            $this->apimodel->errornumber = 0;

                            $this->apimodel->insert();

                            $this->apimodel->id = $this->apimodel->selectId();

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
                                    $smtp = $this->pdo->executeStatement($sql);
                                    if ($smtp) {
                                        $record = $smtp->fetchAll();
                                        if (count($record) > 0) {
                                            foreach ($record as $row) {
                                                if (intval($row['db']) === 1) {
                                                    $smtp = $this->makeUpdate($model, $keys, $values, $values[array_search('Id', $keys)]);
                                                } else {
                                                    $smtp = $this->makeInsert($model, $keys, $values);
                                                }
                                            }
                                        }
                                    } else {
                                        return $sql . ' hibával tért vissza!';
                                    }
                                    $return = $this->pdo->executeStatementReturnFail($smtp);
                                    if (gettype($return) != "object") {
                                        if (strpos($return, "Failed") > 0) {
                                            $this->apimodelerrorInsert($smtp, $return);
                                            $this->apimodel->errornumber++;
                                        }
                                    }
                                }
                                $this->apimodel->updateErrornumber();
                            }
                        }
                    }
                }
                $this->utility->fileUnlink(PATH_XML.$file);
            }
        }

        $txt = "End: ". date('Y.m.d h:m:s', strtotime('now')) . "\n";
        fwrite($this->outputFile, $txt);
        fclose($this->outputFile);

    }
}



