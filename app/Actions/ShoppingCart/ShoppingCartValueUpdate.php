<?php

namespace App\Actions\ShoppingCart;

class ShoppingCartValueUpdate
{
    public function handle($shoppingCart, $netValue, $vatValue) {
        $shoppingCart->NetValue = $shoppingCart->NetValue + $netValue;
        $shoppingCart->GrossValue = $shoppingCart->GrossValue + $netValue + $vatValue;
        $shoppingCart->VatValue = $shoppingCart->VatValue + $vatValue;
        $shoppingCart->save();
    }
}
