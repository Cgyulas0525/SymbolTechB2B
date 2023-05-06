<?php

namespace App\Actions\ShoppingCartDetail;

use App\Actions\ShoppingCart\ShoppingCartInsertGetIdAction;
use App\Actions\ShoppingCart\ShoppingCartValueUpdate;
use App\Classes\productPriceClass;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;
use Carbon\Carbon;

use myUser;

class OneDetailToOpenedShoppingCartAction
{

    public function handle($id) {

        $shoppingCartDetailFrom = ShoppingCartDetail::with('productRelation.vatRelation')->find($id);
        $shoppingCart = ShoppingCart::OwnOpen(myUser::user()->customerId, myUser::user()->customercontact_id)->first();

        if ( !empty($shoppingCartDetailFrom)) {

            $product = Product::with('vatRelation')->find($shoppingCartDetailFrom->Product);

            $shoppingCartDetail = ShoppingCartDetail::where('ShoppingCart', $shoppingCart->Id)->where('Product', $shoppingCartDetailFrom->Product)->first();

            $productPrice = productPriceClass::getProductPrice($shoppingCartDetailFrom->Product, $shoppingCartDetailFrom->Quantity, $product->QuantityUnit, $shoppingCartDetailFrom->Currency);

            $quantity = $shoppingCartDetailFrom->Quantity + (!empty($shoppingCartDetail) ? $shoppingCartDetail->Quantity : 0);

            $netValue = $quantity * $productPrice;
            $vatValue = ($quantity * $productPrice  * $shoppingCartDetailFrom->productRelation->vatRelation->Rate) / 100;

            if ( empty($shoppingCart)) {

                $action = new ShoppingCartInsertGetIdAction();
                $action->handle($netValue, $vatValue);

            } else {

                $action = new ShoppingCartValueUpdate();
                $action->handle($shoppingCart, $netValue, $vatValue);
            }


            if ( empty($shoppingCartDetail) ) {

                $action = new ShoppingCartDetailInsertGetIdAction();
                $action->handle($shoppingCart, $product->Id, $product, $shoppingCartDetailFrom->Quantity, $productPrice, $netValue, $vatValue);

            } else {

                ShoppingCartDetail::where('Id', $shoppingCartDetail->Id)
                    ->update([
                        'Quantity' => $quantity,
                        'UnitPrice' => $productPrice,
                        'NetValue' => $netValue,
                        'GrossValue' => $netValue + $vatValue,
                        'VatValue' => $vatValue,
                        'updated_at' => Carbon::now()
                    ]);

            }
        }

    }
}
