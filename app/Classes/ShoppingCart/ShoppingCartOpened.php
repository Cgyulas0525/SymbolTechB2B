<?php
namespace App\Classes\ShoppingCart;

use App\Models\ShoppingCart;
use myUser;

class ShoppingCartOpened {

    public function isOpenedShoppingCart() {
        return ShoppingCart::where('CustomerContact', myUser::user()->customercontact_id)->where('Opened', 0)->get()->count();
    }

    public function openedShoppingCart() {
        return ShoppingCart::where('CustomerContact', myUser::user()->customercontact_id)->where('Opened', 0)->first();
    }


}
