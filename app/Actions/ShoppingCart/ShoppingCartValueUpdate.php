<?php

namespace App\Actions\ShoppingCart;

use DB;

class ShoppingCartValueUpdate
{
    public function handle($shoppingCart, $netValue, $vatValue) {

       DB::table('shoppingCart')
           ->where('Id', $shoppingCart->Id)
            ->update([
                'NetValue' => $shoppingCart->NetValue + $netValue,
                'GrossValue' => $shoppingCart->GrossValue + $netValue + $vatValue,
                'VatValue' => $shoppingCart->VatValue + $vatValue
            ]);

    }
}
