<?php

namespace App\Actions\ShoppingCart;

use \Carbon\Carbon;

class ShoppingCartValueUpdate
{
    public function handle($shoppingCart, $shoppingCartDetail) {

        $shoppingCart->NetValue   = $shoppingCart->NetValue - $shoppingCartDetail->NetValue;
        $shoppingCart->VatValue   = $shoppingCart->VatValue - $shoppingCartDetail->VatValue;
        $shoppingCart->GrossValue = $shoppingCart->GrossValue - $shoppingCartDetail->GrossValue;
        $shoppingCart->updated_at = Carbon::now();
        $shoppingCart->save();

        return $shoppingCart;

    }
}
