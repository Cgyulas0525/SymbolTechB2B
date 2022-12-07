<?php
namespace App\Classes;

use App\Models\ProductCustomerCode;
use Illuminate\Http\Request;
use myUser;
use Response;
use logClass;
use DB;

use App\Models\ExcelImport;

Class excelImportClass{

    public static function headLine()
    {
        $data = ExcelImport::where('user_id', myUser::user()->id)->first();
        $array = [];
        if (!empty($data)) {
            $array = $data->attributesToArray();
            $keys = array_keys($array);
            $values = array_values($array);
            for ( $i = 0; $i < count($keys); $i++) {
                if (is_null($values[$i]) || $keys[$i] == "created_at" || $keys[$i] == "updated_at" || $keys[$i] == "deleted_at") {
                    unset($array[$keys[$i]]);
                }
            }
        }
        return $array;
    }

}

