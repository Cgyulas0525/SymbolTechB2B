<?php

namespace App\Traits\Others;

use App\Classes\myFtp;
use App\Models\Customer;
use Illuminate\Http\Request;

use DB;

trait PostCustomerPriceTrait {

    public function postCustomerPrice(Request $request) {

        if ($request->has('customer')) {
            $datas = Response::json(DB::table('product as t1')
                ->select('t1.Name', DB::raw('getProductPrice('.$request->customer.', t1.Id, 1, -1, -1) as productprice' ), DB::raw('getProductCustomerCode('.$request->customer.', t1.Id) as code' ))
                ->where('t1.Service', 0)
                ->where('t1.Deleted', 0)
                ->where('t1.Id', '<', 100)
                ->get());

            $customer = Customer::where('Id', $request->customer)->first();

            $fname = "customerprice_" . $customer->Name .".json";
            $file = getenv('FILE_UPLOAD').'/' .$fname;
            file_put_contents($file, $datas);

            if (!empty(getenv('FTP_HOST'))) {
                $myFtp = new myFtp($file, $fname);
                $myFtp->uploadFile();
            }
        }

        return view('home');
    }

}
