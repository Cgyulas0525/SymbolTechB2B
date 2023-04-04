<?php

namespace App\Actions\ShoppingCart;

use App\Classes\ShoppingCart\voucherNumber;
use myUser;
use App\Classes\logClass;
use Carbon\Carbon;
use DB;

class ShoppingCartInsertGetIdAction
{
    public function handle($netValue, $vatValue) {
        $scId = DB::table('ShoppingCart')
            ->insertGetId([
                'VoucherNumber' => voucherNumber::nextB2BVoucherNumber(),
                'Customer' => myUser::user()->customerId,
                'CustomerAddress' => myUser::user()->CustomerAddressId,
                'CustomerContact' => myUser::user()->customercontact_id,
                'VoucherDate' => Carbon::now(),
                'PaymentMethod' => -1,
                'Currency' => -1,
                'CurrencyRate' => 1,
                'TransportMode' => -2,
                'NetValue' => $netValue,
                'GrossValue' => $netValue + $vatValue,
                'VatValue' => $vatValue,
                'created_at' => Carbon::now()
            ]);
        logClass::insertDeleteRecord( 1, "ShoppingCart", $scId);

    }
}
