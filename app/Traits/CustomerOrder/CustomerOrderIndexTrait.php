<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexTrait {

    public function customerOrderIndex(Request $request, $customerContact, $year)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();

                return $cos->dwData($cos->allData($customerContact, $year));

            }

            return view('customer_orders.index')->with(['customerContact' => $customerContact != -99999 ? 0 : 1,
                                                             'year' => $year]);
        }

    }

}
