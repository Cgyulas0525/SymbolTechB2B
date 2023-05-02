<?php

namespace App\Actions\ShoppingCart;

use App\Actions\ShoppingCartDetail\ShoppingCartDetailInsertGetIdAction;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailObserverAction;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;


use App\Classes\productPriceClass;
use App\Services\ShoppingCartDetailService;
use Illuminate\Http\Request;

class OneRecordCopyShoppingCartDetailToShoppingCartAction
{

    public function handle($id, $productId)
    {

        $shoppingCartDetailFrom = ShoppingCartDetail::find($id);

        if ( !empty($shoppingCartDetailFrom)) {

            $shoppingCart = ShoppingCart::OwnOpen()->first();
            $shoppingCartDetail = ShoppingCartDetail::where('ShoppingCart', $shoppingCart->Id)->where('Product', $productId)->first();

            $product = Product::with('vatRelation')->find($productId);
            $productPrice = productPriceClass::getProductPrice($productId, $shoppingCartDetailFrom->Quantity, $product->QuantityUnit, $shoppingCartDetailFrom->Currency);

            $netValue = ($shoppingCartDetailFrom->Quantity * $productPrice);
            $vatValue = ($shoppingCartDetailFrom->Quantity * $productPrice * $product->vatRelation->Rate) / 100;


            if ( empty($shoppingCartDetail) ) {

                if ( empty($shoppingCart)) {

                    $action = new ShoppingCartInsertGetIdAction();
                    $action->handle($netValue, $vatValue);

                } else {

                    $action = new ShoppingCartValueUpdate();
                    $action->handle($shoppingCart, $netValue, $vatValue);

                }

                $action = new ShoppingCartDetailInsertGetIdAction();
                $action->handle($shoppingCart, $productId, $product, 0, $productPrice, $netValue, $vatValue);

            } else {

                $request = new Request();

                $request->replace([
                    'Id' => $shoppingCartDetail->Id,
                    'Quantity' => $shoppingCartDetail->Quantity + $shoppingCartDetailFrom->Quantity,
                    'NetValue' => $shoppingCartDetail->NetValue + $netValue,
                    'VatValue' => $shoppingCartDetail->VatValue + $vatValue,
                    'GrossValue' => $shoppingCartDetail->GrossValue + $netValue + $vatValue,
                ]);

                $scds = new ShoppingCartDetailService();
                $scds->shoppingCartDetailValueUpdate($request);

                $sco = new ShoppingCartDetailObserverAction();
                $sco->handle();

            }
        }
    }

}
