<?php

namespace App\Services;

use App\Classes\logClass;
use App\Classes\utilityClass;
use App\Models\ShoppingCart;
use Carbon\Carbon;

use App\Services\ShoppingCartDetailService;

class ShoppingCartService
{

    public function shoppingCartInsert($input) {
        $shoppingCart = new ShoppingCart;

        $shoppingCart->VoucherNumber    = $input['VoucherNumber'];
        $shoppingCart->Customer         = myUser::user()->customerId;
        $shoppingCart->CustomerAddress  = $input['CustomerAddress'];
        $shoppingCart->CustomerContact  = myUser::user()->customercontact_id;
        $shoppingCart->VoucherNumber    = $input['VoucherNumber'];
        $shoppingCart->VoucherDate      = $input['VoucherDate'];
        $shoppingCart->DeliveryDate     = $input['DeliveryDate'];
        $shoppingCart->PaymentMethod    = $input['PaymentMethod'];
        $shoppingCart->Currency         = utilityClass::currencyId('HUF');
        $shoppingCart->CurrencyRate     = 1;
        $shoppingCart->TransportMode    = $input['TransportMode'];
        $shoppingCart->CustomerContract = NULL;
        $shoppingCart->DepositValue     = $input['DepositValue'];
        $shoppingCart->DepositPercent   = $input['DepositPercent'];
        $shoppingCart->NetValue         = $input['NetValue'];
        $shoppingCart->GrossValue       = $input['GrossValue'];
        $shoppingCart->VatValue         = $input['VatValue'];
        $shoppingCart->Comment          = $input['Comment'];
        $shoppingCart->Opened           = 0;
        $shoppingCart->CustomerOrder    = NULL;

        $shoppingCart->save();

        logClass::insertDeleteRecord( 1, "ShoppingCart", $shoppingCart->Id);

    }

    public function shoppingCartUpdate($input, $id) {

        $shoppingCart = ShoppingCart::find($id);

        $shoppingCart->VoucherNumber    = $input['VoucherNumber'];
        $shoppingCart->Customer         = $input['Customer'];
        $shoppingCart->CustomerAddress  = $input['CustomerAddress'];
        $shoppingCart->CustomerContact  = $input['CustomerContact'];
        $shoppingCart->VoucherNumber    = $input['VoucherNumber'];
        $shoppingCart->VoucherDate      = $input['VoucherDate'];
        $shoppingCart->DeliveryDate     = $input['DeliveryDate'];
        $shoppingCart->PaymentMethod    = $input['PaymentMethod'];
        $shoppingCart->Currency         = $input['Currency'];
        $shoppingCart->CurrencyRate     = $input['CurrencyRate'];
        $shoppingCart->TransportMode    = $input['TransportMode'];
        $shoppingCart->CustomerContract = $input['CustomerContact'];
        $shoppingCart->DepositValue     = $input['DepositValue'];
        $shoppingCart->DepositPercent   = $input['DepositPercent'];
        $shoppingCart->NetValue         = $input['NetValue'];
        $shoppingCart->GrossValue       = $input['GrossValue'];
        $shoppingCart->VatValue         = $input['VatValue'];
        $shoppingCart->Comment          = $input['Comment'];
        $shoppingCart->Opened           = $input['Opened'];
        $shoppingCart->CustomerOrder    = $input['CustomerOrder'];

        $shoppingCart->save();

        return $shoppingCart;

    }

    public function shoppingCartValueUpdate($shoppingCart, $shoppingCartDetail) {

        $shoppingCart->NetValue   = $shoppingCart->NetValue - $shoppingCartDetail->NetValue;
        $shoppingCart->VatValue   = $shoppingCart->VatValue - $shoppingCartDetail->VatValue;
        $shoppingCart->GrossValue = $shoppingCart->GrossValue - $shoppingCartDetail->GrossValue;
        $shoppingCart->updated_at = Carbon::now();
        $shoppingCart->save();

        return $shoppingCart;

    }

    public function shoppingCartDelete($shoppingCart, ShoppingCartDetailService $shoppingCartDetailService) {

        $shoppingCartDetailService->shoppingCartDetailsDelete($shoppingCart->Id);
        $shoppingCart->deleted_at = Carbon::now();
        $shoppingCart->save();

        logClass::insertDeleteRecord( 5, "ShoppingCart", $shoppingCart->Id);

    }

    public function shoppingCartOpenedUpdate($shoppingCart)
    {

        $modifiedShoppingCart = $shoppingCart;

        $modifiedShoppingCart->updated_at = Carbon::now();
        $modifiedShoppingCart->Opened = 0;
        $modifiedShoppingCart->save();

        logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

    }

}
