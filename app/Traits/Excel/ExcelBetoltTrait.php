<?php

namespace App\Traits\Excel;

use App\Classes\langClass;
use App\Imports\excelImportImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use myUser;
use Flash;

trait ExcelBetoltTrait {
    public function excelBetolt(Request $request)
    {
        $file = $request->file('import_file');

        if (empty($request->import_file)) {
            Flash::error(langClass::trans('Nem adott meg filet!'))->important();
        } else {

            DB::table('excelimport')->where('user_id', myUser::user()->id)->delete();
            Excel::import(new excelImportImport, $request->import_file);

        }

        return back();
    }

}
