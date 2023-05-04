<?php

namespace App\Traits\CustomerOrder;

use Illuminate\Http\Request;
use DB;
use DataTables;
use myUser;

trait CustomerOrderDetailIndexTrait {

    public function customerOrderDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = DB::table('customerorderdetail as t1')
                    ->select('t1.*', 't2.Name as CurrencyName', 't3.Name as ProductName', 't4.Name as QuantityUnitName', 't5.Name as VatName', 't5.Rate as VatRate', 't6.Name as StatusName')
                    ->join('currency as t2', 't2.Id', '=', 't1.Currency')
                    ->join('product as t3', 't3.Id', '=', 't1.Product')
                    ->join('quantityunit as t4', 't4.Id', '=', 't1.QuantityUnit')
                    ->join('vat as t5', 't5.Id', '=', 't1.Vat')
                    ->leftJoin('customerorderdetailstatus as t6', 't6.Id', '=', 't1.DetailStatus')
                    ->where('t1.CustomerOrder', $id)
                    ->where('t3.Service', 0)
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('CurrencyName', function($data) { return $data->CurrencyName; })
                    ->addColumn('ProductName', function($data) { return $data->ProductName; })
                    ->addColumn('QuantityUnitName', function($data) { return $data->QuantityUnitName; })
                    ->addColumn('VatRate', function($data) { return $data->VatRate; })
                    ->addColumn('StatusName', function($data) { return $data->StatusName; })
                    ->make(true);

            }

            return view('customer_orders.index');
        }
    }

}


