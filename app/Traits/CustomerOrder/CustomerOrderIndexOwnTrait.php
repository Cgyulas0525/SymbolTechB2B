<?php

namespace App\Traits\CustomerOrder;

use App\Services\CustomerOrderService;
use Illuminate\Http\Request;
use myUser;

trait CustomerOrderIndexOwnTrait {

    public function indexOwn(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new CustomerOrderService();

                return $cos->dwData($cos->allData()->where('t1.CustomerContact', myUser::user()->customercontact_id));

            }

            return view('customer_orders.index');
        }

    }

}
