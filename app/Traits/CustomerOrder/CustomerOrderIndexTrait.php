<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexTrait {

    public function index(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();

                return $cos->dwData($cos->allData());

            }

            return view('customer_orders.index');
        }

    }

}
