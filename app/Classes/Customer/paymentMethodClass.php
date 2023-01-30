<?php

namespace App\Classes\Customer;

use myUser;
use App\Models\Customer;
use App\Models\CustomerAddress;

class paymentMethodClass {

    public static function customerPaymentMethod()
    {
        $paymentMethod = NULL;
        if ( !empty(myUser::user()->CustomerAddress) ) {
            $paymentMethod = CustomerAddress::where('Id', myUser::user()->CustomerAddress)->first()->PaymentMethod;
            if ( empty($paymentMethod) ) {
                if ( !empty(myUser::user()->customerId) ) {
                    $paymentMethod = Customer::where('Id', myUser::user()->customerId)->first()->PaymentMethod;
                }
            }
        } else {
            if ( !empty(myUser::user()->customerId) ) {
                $paymentMethod = Customer::where('Id', myUser::user()->customerId)->first()->PaymentMethod;
            }
        }

        $data = !empty($paymentMethod) ? $paymentMethod : NULL;
    }


}
