<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexCOLastThreeMonthTrait {

    public function indexCOLastTreeMonth(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();
                $begin = date('Y-m-d', strtotime('today - 3 months'));
                $end   = date('Y-m-d', strtotime('today'));

                return $cos->dwData($cos->allData()->whereBetween('VoucherDate', [$begin, $end]));

            }

            return view('customer_orders.index');
        }

    }

}
