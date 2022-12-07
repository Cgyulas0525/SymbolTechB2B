<?php
namespace App\Classes;

use App\Models\Employee;
use App\Models\PaymentMethod;
use DB;
use App\Models\VoucherSequence;
use App\Models\CustomerOrder;
use App\Models\Currency;

Class utilityClass{

    /*
     * .env value change
     *
     * @param $key string
     * @param $value string
     *
     * @return void
     */
    public static function changeEnvironmentVariable($key,$value)
    {
        $path = base_path('.env');

        if(is_bool(env($key)))
        {
            $old = env($key)? 'true' : 'false';
        }
        elseif(env($key)===null){
            $old = 'null';
        }
        else{
            $old = env($key);
        }

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key."=".$old, $key."=".$value, file_get_contents($path)
            ));
        }

    }

    /*
     * Budapesti most...
     *
     * @return now datetime
     */
    public static function now()
    {
        $timezone = "Europe/Budapest";
        date_default_timezone_set($timezone);
        return date("Y-m-d H:i:s", strtotime('now'));
    }

    /*
     * Belső felhasználó picture a mySQl user id alapján
     *
     * @param $id integer
     *
     * @return employee->picture blob
     */
    public static function getEmployeePicture($id) {
        $employee = Employee::where('Id', function ($query) use($id) {
            return $query->from('users')->select('employee_id')->where('id', $id)->first();
        })->first();
        return !empty($employee->Picture) ? $employee->Picture : NULL;
    }

    /* Nincs kép hexadecimálissá alakítás
     *
     * @return hexadecimal
     */
    public static function noAviablePicture()
    {
        $filename = public_path('img/noAviableImage.jpg');
        $handle = fopen($filename, "rb");
        $contents =  bin2hex ( fread( $handle, filesize($filename) ) );
        fclose($handle);
        return $contents;
    }

    /*
     * a blobban tárolt hexadecimális kép megjelenítéséhez...
     *
     * @param $picture binary
     *
     * @return base64
     */
    public static function echoPicture($picture)
    {
        $picture = $picture != NULL ? $picture : session('noAviablePicture');
        return base64_encode(pack("H" . strlen($picture), $picture));

    }

    /*
     * XML struktúrából visszaadja a php tömböt
     *
     * @param $xmlFile xml file
     *
     * $return phpArray json
     */
    public static function XMLArray($xmlFile)
    {
        $xmlDataString = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlFile);
        $xmlObject = simplexml_load_string($xmlDataString);
        $json = json_encode($xmlObject);
        return json_decode($json, true);
    }

    /*
     * Az XML tömb adott kulcsáhpz tartozó érték
     *
     * @param $phpDataArray xml tömb json formátumben
     * @param $key string
     *
     * @return $value
     */
    public static function XMLValue($phpDataArray, $key)
    {
        $values = array_values($phpDataArray);
        $i = array_search($key, array_keys($phpDataArray));
        return $values[$i];
    }

    /*
     * Az adott év, adott bizonylat típusból az utolsó bizonylatszám a CustomerOrder táblában
     *
     * @param $voucherSequence integer
     * @param $ev integer
     *
     * @return string
     */
    public static function maxVocherNumber($voucherSequence, $ev)
    {
        $vs = VoucherSequence::find($voucherSequence)->Mask;
        $evpos = strpos( $vs, '$EV');
        $eleje = substr($vs, 0, $evpos);
        $max = CustomerOrder::where(DB::raw('SUBSTR(VoucherNumber,'. ($evpos + 1) .','. 4 . ')'), $ev )
            ->where('VoucherSequence', $voucherSequence)
            ->get()->max();
        return $max->VoucherNumber ?? $eleje . $ev . '-00000';
    }

    /*
     * A következő bizonylatszám
     *
     * @param $voucherNumber
     *
     * @return string
     */
    public static function nextVoucherNumber($voucherNumber)
    {
        $eleje = substr($voucherNumber, 0, (strlen($voucherNumber) - 5));
        $sorszam = str_pad(strval(intval(substr($voucherNumber, -5)) + 1),4,"0",STR_PAD_LEFT);

        return $eleje . $sorszam;
    }

    public static function offerColLg($count)
    {
        if ($count == 1) {
            $number = 12;
        } elseif ($count == 2 ) {
            $number = 6;
        } elseif ($count == 3) {
            $number = 4;
        } elseif ($count >= 4 ) {
            $number = 3;
        }
        return 'col-lg-' . $number . ' col-md-6 col-xs-12 topmargin1em';
    }

    /*
     * Pénznem id a neve alapján
     *
     * @param $name string
     *
     * @return $id integer;
     */
    public static function currencyId($name)
    {
        $currency = Currency::where('Deleted', 0)->where('Name', $name)->first();
        return !empty($currency) ? $currency->Id : NULL;
    }

    public static function paymentMethodName($id)
    {
        return PaymentMethod::where('id', $id)->first()->Name;
    }
}
