<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ShoppingCartDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Classes\logClass;

use productPriceClass;

class ShoppingCartDetailService
{
    public function shoppingCartDetailDelete($shoppingCartDetail) {

        $shoppingCartDetail->deleted_at = Carbon::now();
        $shoppingCartDetail->save();
        logClass::insertDeleteRecord( 7, "ShoppingCartDetail", $shoppingCartDetail->Id);

    }

    public function shoppingCartDetailsDelete($id) {
        $details = ShoppingCartDetail::where('ShoppingCart', $id)->get();

        foreach ( $details as $detail ) {
            $this->shoppingCartDetailDelete($detail);
        }

    }

    public function shoppingCartDetailValueUpdate($request) {

        $old = ShoppingCartDetail::find($request->get('Id'));

        DB::table('ShoppingCartDetail')
            ->where('Id', $request->get('Id'))
            ->update([
                'Quantity'         => $request->get('Quantity'),
                'NetValue'         => $request->get('NetValue'),
                'VatValue'         => $request->get('VatValue'),
                'GrossValue'       => $request->get('GrossValue'),
                'updated_at'       => Carbon::now()
            ]);

        $new = ShoppingCartDetail::find($request->get('Id'));

        logClass::modifyRecord( "ShoppingCartDetail", $old, $new);

    }

    public function shoppingCartDetailUpdate($request) {

        $shoppingCartDetail = ShoppingCartDetail::with('vatRelation')
                                                ->where('ShoppingCart', $request->get('Id'))
                                                ->where('Product', $request->get('Product'))
                                                ->whereNull('deleted_at')
                                                ->firstOrFail();

        if (!empty($shoppingCartDetail)) {

            DB::table('ShoppingCartDetail')
                ->where('Id', $shoppingCartDetail->Id)
                ->update([
                    'Quantity'   => $shoppingCartDetail->Quantity + $request->get('Quantity'),
                    'NetValue'   => ($shoppingCartDetail->Quantity + $request->get('Quantity')) * $shoppingCartDetail->UnitPrice,
                    'VatValue'   => ($shoppingCartDetail->Quantity + $request->get('Quantity')) * ( $shoppingCartDetail->UnitPrice * ( $shoppingCartDetail->vatRelation->Rate / 100 )),
                    'GrossValue' => ( ($shoppingCartDetail->Quantity + $request->get('Quantity')) * $shoppingCartDetail->UnitPrice ) + ( ($shoppingCartDetail->Quantity + $request->get('Quantity')) * ( $shoppingCartDetail->UnitPrice * ( $shoppingCartDetail->vatRelation->Rate / 100 ))),
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            logClass::modifyRecord( "ShoppingCartDetail", $shoppingCartDetail, ShoppingCartDetail::find($shoppingCartDetail->Id));

        }

    }

    public function shoppingCartDetailInsert($request) {

        $product      = Product::with('vatRelation')->find($request->get('Product'));
        $productPrice = productPriceClass::getProductPrice($product->Id, $request->get('Quantity'), $product->QuantityUnit, -1);

        $newShoppingCartDetail = new ShoppingCartDetail;

        $newShoppingCartDetail->ShoppingCart = $request->get('Id');
        $newShoppingCartDetail->Currency     = -1;
        $newShoppingCartDetail->CurrencyRate = 1;
        $newShoppingCartDetail->Product      = $request->get('Product');
        $newShoppingCartDetail->Vat          = $product->Vat;
        $newShoppingCartDetail->QuantityUnit = $product->QuantityUnit;
        $newShoppingCartDetail->Quantity     = $request->get('Quantity');
        $newShoppingCartDetail->UnitPrice    = $productPrice;
        $newShoppingCartDetail->NetValue     = $request->get('Quantity') * $productPrice ;
        $newShoppingCartDetail->VatValue     = ($request->get('Quantity') * $productPrice) * ($product->vatRelation->Rate / 100);
        $newShoppingCartDetail->GrossValue   = ($request->get('Quantity') * $productPrice) * (( 100 + $product->vatRelation->Rate) / 100);
        $newShoppingCartDetail->created_at   = Carbon::now();

        $newShoppingCartDetail->save();

        logClass::insertDeleteRecord( 5, "ShoppingCartDetail", $newShoppingCartDetail->Id);

    }
}
