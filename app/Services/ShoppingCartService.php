<?php

namespace App\Services;

use App\Classes\logClass;
use App\Classes\utilityClass;
use App\Models\ShoppingCart;
use Carbon\Carbon;
use DB;

use App\Services\ShoppingCartDetailService;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailsDelete;

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

        ShoppingCart::where('Id', $id)
            ->update([
                    'VoucherNumber'    => $input['VoucherNumber'],
                    'Customer'         => $input['Customer'],
                    'CustomerAddress'  => $input['CustomerAddress'],
                    'CustomerContact'  => $input['CustomerContact'],
                    'VoucherDate'      => $input['VoucherDate'],
                    'DeliveryDate'     => $input['DeliveryDate'],
                    'PaymentMethod'    => $input['PaymentMethod'],
                    'Currency'         => $input['Currency'],
                    'CurrencyRate'     => $input['CurrencyRate'],
                    'TransportMode'    => $input['TransportMode'],
                    'CustomerContract' => $input['CustomerContact'],
                    'DepositValue'     => $input['DepositValue'],
                    'DepositPercent'   => $input['DepositPercent'],
                    'NetValue'         => (float)$input['NetValue'],
                    'GrossValue'       => (float)$input['GrossValue'],
                    'VatValue'         => (float)$input['VatValue'],
                    'Comment'          => $input['Comment'],
                    'Opened'           => $input['Opened'],
                    'CustomerOrder'    => $input['CustomerOrder']
                ]);

        return ShoppingCart::find($id);

    }

    public function shoppingCartValueUpdate($shoppingCart, $shoppingCartDetail) {

        $shoppingCart->NetValue   = $shoppingCart->NetValue - $shoppingCartDetail->NetValue;
        $shoppingCart->VatValue   = $shoppingCart->VatValue - $shoppingCartDetail->VatValue;
        $shoppingCart->GrossValue = $shoppingCart->GrossValue - $shoppingCartDetail->GrossValue;
        $shoppingCart->updated_at = Carbon::now();
        $shoppingCart->save();

        return $shoppingCart;

    }

    public function shoppingCartDelete($shoppingCart) {

        DB::beginTransaction();

        try {

            $scdd = new ShoppingCartDetailsDelete($shoppingCart->Id);
            $scdd->handle();

            $shoppingCart->deleted_at = Carbon::now();
            $shoppingCart->save();

            logClass::insertDeleteRecord( 5, "ShoppingCart", $shoppingCart->Id);

            DB::commit();


        } catch (\Exception $e) {

            DB::rollBack();

        }

    }

    public function shoppingCartOpenedUpdate($shoppingCart) {

        $modifiedShoppingCart = $shoppingCart;

        $modifiedShoppingCart->updated_at = Carbon::now();
        $modifiedShoppingCart->Opened = 0;
        $modifiedShoppingCart->save();

        logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

    }

    public function shoppingCartUpdateValueModify($request) {

        $shoppingCart = ShoppingCart::find($request->get('Id'));
        $shoppingCart->NetValue = $request->get('NetValue');
        $shoppingCart->VatValue = $request->get('VatValue');
        $shoppingCart->GrossValue = $request->get('GrossValue');
        $shoppingCart->updated_at = Carbon::now();
        $shoppingCart->save();

        return $shoppingCart;

    }

}
