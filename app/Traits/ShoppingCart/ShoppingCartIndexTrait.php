<?php

namespace App\Traits\ShoppingCart;

use Illuminate\Http\Request;
use myUser;
use App\Services\ShoppingCartIndexService;

trait ShoppingCartIndexTrait {

    public function shoppingCartIndex(Request $request, $customerContact, $year) {

        if( myUser::check() ){

            if ($request->ajax()) {

                $cos = new ShoppingCartIndexService();

                return $cos->dwData($cos->allData($customerContact, $year));

            }

            return view('shopping_carts.index')->with(['customerContact' => $customerContact != -99999 ? 0 : 1,
                                                            'year' => $year]);
        }

    }

}
