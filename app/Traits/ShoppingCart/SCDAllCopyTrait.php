<?php

namespace App\Traits\ShoppingCart;

use App\Actions\ShoppingCartDetail\AllDetailToOpenedShoppingCartAction;
use myUser;

trait SCDAllCopyTrait {

    public function scdAllCopy($id) {

        $action = new AllDetailToOpenedShoppingCartAction();

        $action->handle($id);

        return redirect(route('shoppingCartIndex', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
            'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]));

    }
}
