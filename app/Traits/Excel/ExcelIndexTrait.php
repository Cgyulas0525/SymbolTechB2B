<?php

namespace App\Traits\Excel;

use App\Models\ExcelImport;
use Illuminate\Http\Request;
use DataTables;
use myUser;

trait ExcelIndexTrait {
    public function excelIndex(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = ExcelImport::where('user_id', myUser::user()->id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }

            return back();
        }
    }

}
