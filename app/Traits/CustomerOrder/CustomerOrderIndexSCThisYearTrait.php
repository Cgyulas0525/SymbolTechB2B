<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexSCThisYearTrait {

    public function indexSCThisYear(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();
                $begin = date('Y-m-d', strtotime('first day of january last year'));
                $end   = date('Y-m-d', strtotime('last day of december this year'));

                return $cos->dwData($cos->allShoppingCart()->whereBetween('VoucherDate', [$begin, $end]));

            }

            return view('customer_orders.index');
        }

    }

}

