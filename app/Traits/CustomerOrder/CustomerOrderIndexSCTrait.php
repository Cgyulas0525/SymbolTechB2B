<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexSCTrait {

    public function indexSC(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();

                return $cos->dwData($cos->allShoppingCart(), true);

            }

            return view('customer_orders.index');
        }

    }

}
