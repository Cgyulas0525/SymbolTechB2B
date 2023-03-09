<?php

namespace App\Classes\Api;

use App\Classes\Api\apiUtilityClass;
use App\Classes\Api\ModelChangeClass;
use App\Classes\Api\Inc\witchStrposClass;
use App\Models\Api;
use App\Models\ApiModel;
use DB;

class getSUXMLClass
{
    private $utility = NULL;
    private $outputFile = NULL;
    private $modelChange = NULL;
    private $api = NULL;
    private $apimodel = NULL;
    private $witchStrpos = NULL;
    private $fileName = NULL;
    private $dbArray = [];


    function __construct() {
        require_once dirname(__DIR__, 2). "/Classes/Api/Inc/config.php";

        $this->modelChange = new ModelChangeClass();
        $this->utility = new apiUtilityClass();
        $this->witchStrpos = new witchStrposClass();
        $this->api = new Api();
        $this->apimodel = new ApiModel();
        $this->fileName = PATH_OUTPUT . 'getXML-' . uniqid() . '.txt';
        $this->outputFile = fopen($this->fileName, "w") or die("Unable to open file!");
        $this->utility->fileWrite($this->outputFile, "B2B getXSD\n");
        $this->utility->fileWrite($this->outputFile, "Start: " . date('Y.m.d h:m:s', strtotime('now')) . "\n");
    }

    public function unzipFile() {
        $files = array_diff(preg_grep('~\.(zip)$~', scandir(PATH_INPUT)), array('.', '..'));

        if (count($files) > 0) {
            foreach ($files as $file) {
                $txt = "ZIP file: " . $file . "\n";
                fwrite($this->outputFile, $txt);

                $this->utility->unZip($file);
                $this->utility->fileUnlink(PATH_INPUT.$file);
            }
        }
        if (count($files) == 0) {
            $this->utility->fileWrite($this->outputFile, "Nem található kicsomagolandó file!\n");
        }
    }

    public function insertApiRecord($file)
    {

        $this->api->filename = $file;
        $this->api->created_at = date('Y-m-d H:i:s', strtotime('now'));
        $this->api->save();

    }

    public function insertApiModelRecord($model, $count) {
        $this->apimodel = new ApiModel();
        $this->apimodel->api_id = $this->api->id;
        $this->apimodel->model = $model;
        $this->apimodel->recordnumber = $count;
        $this->apimodel->insertednumber = 0;
        $this->apimodel->updatednumber = 0;
        $this->apimodel->errornumber = 0;
        $this->apimodel->save();
    }

    public function recordMaker($record, $model, $keys, $values) {
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
                                $record->$keyName = $values[$i];
                                break;
                            case "string" :
                                if (is_array($values[$i])) {
                                    if ( count(array_values($values[$i])) == 0) {
                                        $value = "";
                                    }
                                } else {
                                    $value = strpos($values[$i], "'") != false ? str_replace("'", " ", $values[$i]) : $values[$i];
                                }
                                $record->$keyName = $value;
                                break;
                            case "datetime" :
                                $record->$keyName = date('%Y-%m-%d %H:%i:%s', strtotime($values[$i]));
                                break;
                            case "decimal:4" :
                                $record->$keyName = $values[$i];
                                break;
                            default :
                                $this->utility->fileWrite($this->outputFile, $fieldName . " mező típusa nem értelmezhető a " . $model . "táblában! \n");
                        }
                        break;
                    }
                }
            }
        }
        return $record;
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
    public function makeUpdate($model, $keys, $values, $id) {

        $model_name =  'App\Models\\'.$model;
        $record = $model_name::find($id);

        $record = $this->recordMaker($record, $model, $keys, $values);

        $this->apimodel->updatednumber++;
        $record->save();

    }

    public function makeInsert($model, $keys, $values) {

        $model_name =  'App\Models\\'.$model;
        $record = new $model_name();
        $record = $this->recordMaker($record, $model, $keys, $values);

        $this->apimodel->insertednumber++;
        $record->save();

    }

    public function process() {

        $this->unzipFile();
        $files = array_diff(preg_grep('~\.(xml)$~', scandir(PATH_INPUT)), array('.', '..'));
        if ( count($files) == 0 ) {
            $this->utility->fileWrite($this->outputFile, "Nem található feldolgozandó file!\n");
        }
        if ( count($files) > 0 ) {
            foreach ($files as $file) {
                $this->utility->fileWrite($this->outputFile, "XML file: " . $file . "\n");
                $this->insertApiRecord($file);

                $phpDataArray = $this->utility->fileLoader(PATH_INPUT . $file);
                for ($j = 0; $j < count($phpDataArray); $j++) {
                    $model = array_keys($phpDataArray)[$j];
                    if ($model != "PlugIn") {

                        $model == 'Lead' ? "Leed" : $model;

                        $modelArray = $this->modelChange->modelRead($model);
                        if (!is_null($modelArray)) {
                            $castsArray = $this->modelChange->modelExchange($modelArray);
                            $this->castsKeys = array_keys($castsArray);
                            $this->castsValues = array_values($castsArray);

                            $count = $model == 'Leed' ? count($phpDataArray['Lead']) : count($phpDataArray[$model]);
                            $this->insertApiModelRecord($model, $count);

                            if ($count > 0) {
                                foreach ($phpDataArray[$model == 'Leed' ? 'Lead' : $model] as $index => $data) {
                                    if (!is_array($data)) {
                                        $keys = array_keys($phpDataArray[$model == 'Leed' ? 'Lead' : $model]);
                                        $values = array_values($phpDataArray[$model == 'Leed' ? 'Lead' : $model]);
                                    } else {
                                        $keys = array_keys($data);
                                        $values = array_values($data);
                                    }

                                    $model_name =  'App\Models\\'.$model;
                                    $record = array_search('Deleted', Schema::getColumnListing('App\Models\\'.$model_name)) ?
                                                                $model_name::where('Id', $values[array_search('Id', $keys)])->where('Deleted', 0)->first() :
                                                                $model_name::where('Id', $values[array_search('Id', $keys)])->first();

                                    if ( !empty($record) ) {
                                        $this->makeUpdate($model, $keys, $values, $values[array_search('Id', $keys)]);
                                    } else {
                                        $this->makeInsert($model, $keys, $values);
                                    }

                                }
                                $this->apimodel->save();
                            }
                        }
                    }
                }
                $this->utility->fileUnlink(PATH_INPUT.$file);
            }

        }
        $this->utility->fileWrite($this->outputFile, "End: " . date('Y.m.d h:m:s', strtotime('now')) . "\n");
        fclose($this->outputFile);
    }
}
