<?php

namespace App\Http\Controllers;

use App\Models\CustomerContact;
use Illuminate\Http\Request;
use App\Exports\DivisionExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

use App\Models\Employee;
use App\Models\Users;

use GoetasWebservices\XML\XSDReader\SchemaReader;

class DivisionExportController extends Controller
{
    /*
    * mySQL user tábla feltöltés a firebird xml import után
     * a CustomerContact táblából
    */
    public static function makeUserRecordsFromCustomerContact()
    {
        $customercontacts = CustomerContact::all();

        foreach( $customercontacts as $customercontact )
        {
            $user = Users::where('customercontact_id', $customercontact->Id)->first();

            if (!empty($user)) {
                if ($customercontact->Name != $user->name) {
                    $user->name = $customercontact->Name;
                }
                if ($customercontact->Email != $user->email) {
                    $user->email = $customercontact->Email;
                }
                if ($customercontact->Deleted === 1 && is_null($user->deleted_at)) {
                    $user->deleted_at = Carbon\Carbon::now();
                }
                $user->save();
            } else {
                $user = Users::create([
                    'name' => $customercontact->Name,
                    'email' => $customercontact->Email,
                    'customercontact_id' => $customercontact->Id,
                    'rendszergazda' => 0,
                    'password' => md5('PASSWORD'. $customercontact->Id),
                    'megjegyzes' => 'PASSWORD'. $customercontact->Id
                ]);
            }
        }
    }


    /*
    * mySQL user tábla feltöltés a firebird xml import után
     * az Eployee táblából
    */
    public static function makeUserRecordsFromEmployee()
    {
        $employees = Employee::all();
        foreach( $employees as $employee )
        {
            $user = Users::where('employee_id', $employee->Id)->first();

            if (!empty($user)) {
                if ($employee->Name != $user->name) {
                    $user->name = $employee->Name;
                }
                if ($employee->Email != $user->email) {
                    $user->email = $employee->Email;
                }
                if ($employee->Deleted === 1 && is_null($user->deleted_at)) {
                    $user->deleted_at = Carbon\Carbon::now();
                }
                $user->save();
            } else {
                $user = Users::create([
                    'name' => $employee->Name,
                    'email' => $employee->Email,
                    'employee_id' => $employee->Id,
                    'rendszergazda' => 1,
                    'password' => md5('PASSWORD'. $employee->Id),
                    'megjegyzes' => 'PASSWORD'. $employee->Id
                ]);
            }
        }
    }

    public static function XSDimport()
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->load('D:/mas/Customer.xsd');
        $doc->save('D:/mas/Customer.xml');
        $xmlfile = file_get_contents('D:/mas/Customer.xml');

        $parseObj = str_replace($doc->lastChild->prefix.':',"",$xmlfile);
//$doc->doc->textContent;
// echo "<pre>";
//print_r($doc->lastChild->prefix);

        $ob = simplexml_load_string($parseObj);
        $json  = json_encode($ob);
        $data = json_decode($json, true);
        echo "<pre>";
        print_r($data);
    }

    /*
     * xml betöltés model alapján
     *
     * Gyula
     * */
    public static function XMLImport()
    {
        $path = env('XML_DIR');

        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {

            $xmlDataString = file_get_contents($path . "//" . $file);

            $xmlDataString = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlDataString);

            $xmlObject = simplexml_load_string($xmlDataString);

            $json = json_encode($xmlObject);
            $phpDataArray = json_decode($json, true);

            for ($j = 0;$j < count($phpDataArray); $j++) {
                $model = array_keys($phpDataArray)[$j];

                // Ellenőrző adatok feldolgozása
                if ($model === "PLugin") {
                    echo "Plugin <br>";
                }

                if ($model != "PlugIn") {

                    if (count($phpDataArray[$model]) > 0) {

                        // Nincs Lead tábla a mySQL adatbázisban!
                        if ($model === "Lead") {
                            $modelName = '\App' . '\\Models' . '\\' . 'Leed';
                        }

                        if ($model != "Lead") {
                            $modelName = '\App' . '\\Models' . '\\' . $model;
                        }

                        $modelItem = new $modelName;

                        foreach ($phpDataArray[$model] as $index => $data) {

                            if (!is_array($data)) {

                                $keys = array_keys($phpDataArray[$model]);
                                $values = array_values($phpDataArray[$model]);

                            } else {

                                $keys = array_keys($data);
                                $values = array_values($data);

                            }

                            for ($i = 0; $i < count($keys); $i++) {

                                $modelItem[$keys[$i]] = $values[$i] ?? 0;

                            }

                            $dataArray = $modelItem->getAttributes();

                            $modelItem = $modelName::find($dataArray['Id']);

                            !empty($modelItem) ? $modelItem->update($dataArray) : $modelName::insert($dataArray);

                            $modelItem = new $modelName;
                        }
                    }
                }
            }
        }
    }

    public function export($table)
    {
        $export = "App\Exports\\" . $table . "Export";

        $datas = DB::connection('firebird')->table($table)->get();

//        return Excel::download(new DivisionExport($datas), 'divisio.xlsx');
        return Excel::download(new $export($datas), $table . ".csv");

    }

    public static function exportXML($table)
    {
        $export = "App\Exports\\" . $table . "Export";

        $datas = DB::connection('firebird')->table($table)->get();

//        return Excel::download(new DivisionExport($datas), 'divisio.xlsx');
        return Excel::download(new $export($datas), $table . ".xml");

    }

    public function mentesTombbe($mibe, $mi)
    {
        for ($i = 0; $i < count($mi); $i++)
        {
            array_push($mibe, $mi[$i]);
        }
        return $mibe;
    }

    /*
     *
     * model fillable és casts és rules tömbjeinek változtatása
     *
     */
    public function modelValtoztatas()
    {

        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=b2b', 'root', 'password');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $mezok = [["'Valami'", "decimal", "4"], ["'MásValami'", "string", '100']];

        if (count($mezok) > 0) {
            $fileName = 'C:\wamp64\www\Laravel\B2B\App\Models\Customer.php';

            $current = file($fileName);
            $values = array_values($current);

            $fillableStart = NULL;
            $fillableEnd = NULL;
            $castsStart = NULL;
            $castsEnd = NULL;
            $rulesStart = NULL;
            $rulesEnd = NULL;

            $fillableArray = [];
            $castsArray = [];
            $rulesArray = [];
            $elejeArray = [];
            $kozepArray = [];
            $kozep2Array = [];
            $vegeArray = [];

            $mentesArray = [];

            for ($i = 0; $i < count($values); $i++)
            {
                if (strpos($values[$i],  "fillable") > 0 )
                {
                    $fillableStart = is_null($fillableStart) ? $i : $fillableStart;
                }
                if ((strpos($values[$i],  "];" ) > 0) && (strlen(trim($values[$i])) === 2))
                {
                    if (is_null($fillableEnd))
                    {
                        $fillableEnd = $i;
                    } else {
                        if ( ( $i > $fillableEnd ) && is_null($castsEnd))
                        {
                            $castsEnd = $i;
                        } else {
                            if ( ( $i > $castsEnd ) && is_null($rulesEnd))
                            {
                                $rulesEnd = $i;
                            }
                        }
                    }
                }
                if (strpos($values[$i],  "casts") > 0 )
                {
                    $castsStart = is_null($castsStart) ? $i : $castsStart;
                }
                if (strpos($values[$i],  "rules =") > 0 )
                {
                    $rulesStart = is_null($rulesStart) ? $i : $rulesStart;
                }
            }


            for ($i = 0; $i <= count($values); $i++)
            {
                if ( $i <= $fillableStart ) {
                    array_push($elejeArray, $values[$i]);
                } elseif ( ($i > $fillableStart) && ($i < $fillableEnd)) {
                    array_push($fillableArray, $values[$i]);
                } elseif ( ($i >= $fillableEnd) && ($i <= $castsStart)) {
                    array_push($kozepArray, $values[$i]);
                } elseif ( ($i >= $castsStart) && ($i < $castsEnd)) {
                    array_push($castsArray, $values[$i]);
                } elseif ( ($i >= $castsEnd) && ($i <= $rulesStart)) {
                    array_push($kozep2Array, $values[$i]);
                } elseif ( ($i >= $rulesStart) && ($i < $rulesEnd)) {
                    array_push($rulesArray, $values[$i]);
                } elseif ( ($i >= $rulesEnd) && ($i < count($values))) {
                    array_push($vegeArray, $values[$i]);
                }
            }

            foreach ($mezok as $mezo)
            {
                $mezoEOL = $mezo[0] . "\n";
                $ujMezoFillable = str_repeat(' ', strpos($fillableArray[0], "'")).$mezoEOL;
                $fillableArray[count($fillableArray) - 1] = substr($fillableArray[count($fillableArray) - 1], 0, iconv_strpos($fillableArray[count($fillableArray) - 1], "\n", 0)) . ','."\n";
                array_push($fillableArray, $ujMezoFillable);
                $ujMezoCasts = str_repeat(' ', strpos($castsArray[0], "'")).$mezo[0] . ' => ' . "'" . $mezo[1] . (!is_null($mezo[2]) ? ':'.$mezo[2] : null) . "'". "\n";
                $castsArray[count($castsArray) - 1] = substr($castsArray[count($castsArray) - 1], 0, iconv_strpos($castsArray[count($castsArray) - 1], "\n", 0)) . ','."\n";
                array_push($castsArray, $ujMezoCasts);

                $fieldName = substr($mezo[0], 1, (strlen($mezo[0]) - 2));
                if (strpos($mezo[1], 'BLOB') != false ) {
                    $type = "BLOB";
                }
                if ($mezo[1] === "string") {
                    $type = 'varchar(' . $mezo[2] . ')';
                } elseif ($mezo[1] === "decimal") {
                    $type = 'decimal(18,' . $mezo[2] . ')';
                }

                $utasitas = 'ALTER TABLE Customer ADD';
                $mit = $utasitas. " ". $fieldName . " " . $type;
                $statement = $pdo->prepare($mit);
                $statement->execute();

            }

            $mentesArray = $this->mentesTombbe($mentesArray, $elejeArray);
            $mentesArray = $this->mentesTombbe($mentesArray, $fillableArray);
            $mentesArray = $this->mentesTombbe($mentesArray, $kozepArray);
            $mentesArray = $this->mentesTombbe($mentesArray, $castsArray);
            $mentesArray = $this->mentesTombbe($mentesArray, $kozep2Array);
            $mentesArray = $this->mentesTombbe($mentesArray, $rulesArray);
            $mentesArray = $this->mentesTombbe($mentesArray, $vegeArray);

            $fp = 'C:\wamp64\www\Laravel\B2B\App\Models\Customer.txt';

            file_put_contents($fp, $mentesArray);
        }


    }
}


