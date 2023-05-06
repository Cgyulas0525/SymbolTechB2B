<?php

namespace App\Actions\ShoppingCart;

use DB;
use Carbon\Carbon;

class ShoppingCartValueUpdate
{
    public function handle($shoppingCart, $netValue, $vatValue) {

       DB::table('shoppingCart')
           ->where('Id', $shoppingCart->Id)
            ->update([
                'NetValue' => $shoppingCart->NetValue + $netValue,
                'GrossValue' => $shoppingCart->GrossValue + $netValue + $vatValue,
                'VatValue' => $shoppingCart->VatValue + $vatValue,
                'updated_at' => Carbon::now()
            ]);

    }
}
