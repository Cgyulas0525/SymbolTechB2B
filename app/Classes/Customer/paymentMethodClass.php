<?php

namespace App\Classes\Customer;

use myUser;
use App\Models\Customer;
use App\Models\CustomerAddress;

class paymentMethodClass {

    public static function customerPaymentMethod()
    {
        $paymentMethod = NULL;
        if ( !is_null(myUser::user()->CustomerAddress) ) {
            $paymentMethod = CustomerAddress::where('Id', myUser::user()->CustomerAddress)->first()->PaymentMethod;
            if ( is_null($paymentMethod) ) {
                if ( !is_null(session('customer_id')) ) {
                    $paymentMethod = Customer::where('Id', session('customer_id'))->first()->PaymentMethod;
                }
            }
        } else {
            if ( !is_null(session('customer_id')) ) {
                $paymentMethod = Customer::where('Id', session('customer_id'))->first()->PaymentMethod;
            }
        }

        return !empty($paymentMethod) ? $paymentMethod : NULL;
    }


}
