<?php

namespace App\Actions\ShoppingCart;

use \Carbon\Carbon;

class ShoppincCartDetailDelete
{
    public function handle($shoppingCartDetail) {

        $shoppingCartDetail->deleted_at = Carbon::now();
        $shoppingCartDetail->save();

    }
}
