<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;
use DB;

trait CustomerOrderIndexCOLastThreeMonthTrait {

    public function indexCOLastTreeMonth(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $begin = date('Y-m-d', strtotime('today - 3 months'));
                $end   = date('Y-m-d', strtotime('today'));

                $data = DB::table('customerorder as t1')
                    ->selectRaw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.NetValue, t1.VatValue, t1.GrossValue , t3.Name as currencyName, SUM(1) as DetailNumber, t4.Name as statusName', )
                    ->join('customerorderdetail as t2', 't2.CustomerOrder', '=', 't1.Id')
                    ->join('currency as t3', 't3.Id', '=', 't1.Currency')
                    ->leftJoin('customerorderstatus as t4', 't4.Id', '=', 't1.CustomerOrderStatus')
                    ->join('product as t5', 't5.Id', '=', 't2.Product')
                    ->where('t1.Customer', myUser::user()->customerId)
                    ->whereBetween('t1.VoucherDate', [$begin, $end])
                    ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.NetValue', 't1.VatValue', 't1.GrossValue', 't3.Name', 't4.Name')
                    ->get();

                $cos = new CustomerOrderService();

                return $cos->dwData($data);

            }

            return view('customer_orders.index');
        }

    }

}
