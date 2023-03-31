<?php

namespace App\Actions\ShoppingCart;

use DB;
use myUser;

class dwRawAction
{
    //
    public function handle($Id) {
        return DB::raw('getLastProductPrice('.  myUser::user()->customerId .','.$Id.', -1, -1) as lastPrice,
                                 getProductPrice('. myUser::user()->customerId .','.$Id.', 1, -1, -1) as productPrice,
                                 discountPercentage('. myUser::user()->customerId .','.$Id.', 1, -1, -1) as discountPercent' );
    }
}
