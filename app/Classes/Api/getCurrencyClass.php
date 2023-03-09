<?php

namespace App\Classes\Api;

use App\Models\Api;
use App\Models\ApiModel;
use App\models\Currency;
USE App\Models\CurrencyRate;

use App\Classes\Api\apiUtilityClass;

class getCurrencyClass
{


    public $bank;
    public $url = 'http://api.napiarfolyam.hu?bank=';
    public $currencyArray = [];
    public $itemkeys = [];
    public $itemvalues = [];

    public $middleRate = 0;
    public $purchaseRate = 0;
    public $sellingRate = 0;

    public $validfrom = NULL;
    public $date = NULL;
    public $outputFile = NULL;
    public $utility = NULL;
    public $api = NULL;
    public $apimodel = NULL;

    public $currency = NULL;

    function __construct($bank) {

        require_once dirname(__DIR__, 2). "/Classes/Api/Inc/config.php";

        $this->bank = $bank;
        $this->url .= $this->bank;
        $this->validfrom = date('Y-m-d H:i:s', strtotime('midnight'));
        $this->date = date('Y-m-d H:i:s', strtotime('now'));
        $this->utility = new apiUtilityClass();
        $this->api = new Api();
        $this->apimodel = new ApiModel();

        $this->outputFile = fopen(PATH_OUTPUT . 'getCurrency-'. uniqid() . '.txt', "w") or die("Unable to open file!");
        $this->utility->fileWrite($this->outputFile, "B2B getCurrency\n");
        $this->utility->fileWrite($this->outputFile, "Start: " . date('Y.m.d H:m:s', strtotime('now')) . "\n");

    }

    public function insertApiRecord()
    {

        $this->api->filename = $this->url;
        $this->api->created_at = $this->date;
        $this->api->save();

        $this->apimodel = new ApiModel();
        $this->apimodel->api_id = $this->api->id;
        $this->apimodel->model = 'CurrencyRate';
        $this->apimodel->recordnumber = 0;
        $this->apimodel->insertednumber = 0;
        $this->apimodel->updatednumber = 0;
        $this->apimodel->errornumber = 0;
        $this->apimodel->save();
    }

    public function getArray()
    {
        $this->currencyArray = $this->utility->fileLoader($this->url);
        $values = array_values($this->currencyArray);

        $values = array_values($values);
        $values = array_values($values[1]);
        $values = array_values($values);

        $this->insertApiRecord();

        for ( $i = 0; $i < count($values); $i++) {

            $arrayValues = array_values($values[$i]);

            for ( $j = 0; $j < count($arrayValues); $j++) {
                $this->itemkeys = array_keys($arrayValues[$j]);
                $this->itemvalues = array_values($arrayValues[$j]);

                $this->itemValues();
                $this->dbEvent();
            }
        }

        $this->apimodel->save();

        $this->utility->fileWrite($this->outputFile, "End: ". date('Y.m.d H:m:s', strtotime('now')) . "\n");
        fclose($this->outputFile);
    }

    public function arrayItem($mit) {
        return array_search($mit , $this->itemkeys);
    }

    public function itemValues() {
        if ($this->arrayItem('kozep')) {
            $this->purchaseRate = array_values($this->itemvalues[$this->arrayItem('kozep')])[0];
            $this->sellingRate = array_values($this->itemvalues[$this->arrayItem('kozep')])[0];
            $this->middleRate = array_values($this->itemvalues[$this->arrayItem('kozep')])[0];
        } else {
            $this->purchaseRate = $this->itemvalues[$this->arrayItem('vetel')];
            $this->sellingRate = $this->itemvalues[$this->arrayItem('eladas')];
            $this->middleRate = Round( ($this->purchaseRate + $this->sellingRate) / 2, $this->currency->RoundDigits);
        }
    }

    public function currencyRateUpdate($id) {
        $this->apimodel->recordnumber++;
        $this->apimodel->updatednumber++;
        $currencyRate = CurrencyRate::where('Currency', $id)->where('Validfrom', $this->validfrom)->first();
        $currencyRate->Rate = $this->middleRate;
        $currencyRate->RateBuy = $this->purchaseRate;
        $currencyRate->RateSell = $this->sellingRate;
        $currencyRate->RowModify = $this->date;
        $currencyRate->save();
    }

    public function currencyRateInsert($id) {
        $this->apimodel->recordnumber++;
        $this->apimodel->insertednumber++;
        $currencyRate = new CurrencyRate();


        $currencyRate->Currency = $id;
        $currencyRate->ValidFrom = $this->validfrom;
        $currencyRate->Rate = $this->middleRate;
        $currencyRate->RateBuy = $this->purchaseRate;
        $currencyRate->RateSell = $this->sellingRate;
        $currencyRate->RowCreate = $this->date;
        $currencyRate->RowModify = $this->date;
        $currencyRate->save();
    }

    public function dbEvent() {
        $this->currency = Currency::where('Name', $this->itemvalues[$this->arrayItem('penznem')])
                                    ->where('Deleted', 0)
                                    ->first();

        if ( !empty($this->currency) ) {
            $txt = "Currency: ". $this->itemvalues[$this->arrayItem('penznem')] . "\n";
            fwrite($this->outputFile, $txt);
            if ( CurrencyRate::where('Currency', $this->currency->Id)->where('Validfrom', $this->validfrom)->get()->count() == 0) {
                $this->currencyRateInsert($this->currency->Id);
            } else {
                $this->currencyRateUpdate($this->currency->Id);
            }
        }
    }

}
