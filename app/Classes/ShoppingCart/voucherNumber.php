<?php

namespace App\Classes\ShoppingCart;

use App\Models\Customer;
use App\Models\ShoppingCart;

class voucherNumber {

    public static function nextB2BVoucherNumber()
    {
        $bizonylatSzam = ShoppingCart::max('VoucherNumber');
        if (is_null($bizonylatSzam)) {
            return "B2B-" . Customer::where('Id', session('customer_id'))->first()->Code . '-00001';
        }
        return "B2B-" . Customer::where('Id', session('customer_id'))->first()->Code . '-'. str_pad(strval(intval(substr($bizonylatSzam, -5)) + 1),5,"0",STR_PAD_LEFT);
    }

}
