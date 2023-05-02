<?php

namespace App\Actions\CustomerOrderDetail;

use App\Actions\ShoppingCart\ShoppingCartInsertGetIdAction;
use App\Actions\ShoppingCart\ShoppingCartValueUpdate;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailInsertGetIdAction;

use App\Actions\ShoppingCartDetail\ShoppingCartDetailObserverAction;

use App\Services\ShoppingCartDetailService;

use App\Models\CustomerOrderDetail;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;

use App\Classes\productPriceClass;

use myUser;
use \Illuminate\Http\Request;

class OrderDetailToShoppingCartAction
{

    public function handle($id, $productId) {

        $customerOrderDetail = CustomerOrderDetail::find($id);

        if ( !empty($customerOrderDetail)) {

            $shoppingCart = ShoppingCart::OwnOpen(myUser::user()->customerId, myUser::user()->customercontact_id)->first();
            $shoppingCartDetail = ShoppingCartDetail::where('ShoppingCart', $shoppingCart->Id)->where('Product', $productId)->first();

            $product = Product::with('vatRelation')->find($productId);
            $productPrice = productPriceClass::getProductPrice($productId, $customerOrderDetail->Quantity, $product->QuantityUnit, $customerOrderDetail->Currency);

            $netValue = ($customerOrderDetail->Quantity * $productPrice);
            $vatValue = ($customerOrderDetail->Quantity * $productPrice * $product->vatRelation->Rate) / 100;

            if ( empty($shoppingCartDetail) ) {

                if ( empty($shoppingCart)) {

                    $action = new ShoppingCartInsertGetIdAction();
                    $action->handle($netValue, $vatValue);

                } else {

                    $action = new ShoppingCartValueUpdate();
                    $action->handle($shoppingCart, $netValue, $vatValue);

                }

                $action = new ShoppingCartDetailInsertGetIdAction();
                $action->handle($shoppingCart, $productId, $product, $customerOrderDetail->Quantity, $productPrice, $netValue, $vatValue);

            } else {

                $request = new Request();

                $request->replace([
                    'Id' => $shoppingCartDetail->Id,
                    'Quantity' => $shoppingCartDetail->Quantity + $customerOrderDetail->Quantity,
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
