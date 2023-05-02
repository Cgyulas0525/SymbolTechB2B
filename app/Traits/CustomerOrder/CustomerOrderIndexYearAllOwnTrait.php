<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexYearAllOwnTrait {

    public function indexYearAllOwn(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();
                $begin = date('Y-m-d', strtotime('first day of january last year'));
                $end   = date('Y-m-d', strtotime('last day of december this year'));

                return $cos->dwData($cos->allData()->where('t1.Customer', myUser::user()->customerId)->where('t1.CustomerContact', myUser::user()->customercontact_id)->whereBetween('VoucherDate', [$begin, $end]));

            }

            return view('customer_orders.index');
        }

    }

}


