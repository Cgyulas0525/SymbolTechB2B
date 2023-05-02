<?php
namespace App\Classes;

use Illuminate\Http\Request;
use myUser;

use productPriceClass;

use App\Models\CustomerOrderDetail;
use App\Models\ShoppingCartDetail;
use App\Models\Product;
use App\Models\ExcelImport;
use App\Models\Vat;
use App\Classes\ShoppingCart\ShoppingCartOpened;

use App\Models\ShoppingCart;

use App\Actions\ShoppingCartDetail\ShoppingCartDetailInsertGetIdAction;
use App\Actions\ShoppingCart\ShoppingCartInsertGetIdAction;
use App\Actions\ShoppingCart\ShoppingCartValueUpdate;

Class shoppingCartClass {

    public function getSCDs(Request $request)
    {
        return ShoppingCartDetail::where('ShoppingCart', $request->get('id'))->get();
    }

//    public static function oneRecorCopyCustomerOrderDetailToShoppingCart($id, $productId)
//    {
//        $customerOrderDetail = CustomerOrderDetail::find($id);
//
//        if ( !empty($customerOrderDetail)) {
//
//            $scc = new ShoppingCartOpened();
//
//            $shoppingCart = ShoppingCart::OwnOpen(myUser::user()->customerId, myUser::user()->customercontact_id)->first();
//            $product = Product::with('vatRelation')->find($productId);
//            $shoppingCartDetail = ShoppingCartDetail::where('ShoppingCart', $shoppingCart->Id)->where('Product', $productId)->first();
//            $productPrice = productPriceClass::getProductPrice($productId, $customerOrderDetail->Quantity, $product->QuantityUnit, $customerOrderDetail->Currency);
//
//            $netValue = ($customerOrderDetail->Quantity * $productPrice);
//            $vatValue = ($customerOrderDetail->Quantity * $productPrice * $product->vatRelation->Rate) / 100;
//
//            if ( empty($shoppingCart)) {
//                $action = new ShoppingCartInsertGetIdAction();
//                $action->handle($netValue, $vatValue);
//            } else {
//                $action = new ShoppingCartValueUpdate();
//                $action->handle($shoppingCart, $netValue, $vatValue);
//            }
//
//
//            if ( empty($shoppingCartDetail) ) {
//                $action = new ShoppingCartDetailInsertGetIdAction();
//                $action->handle($shoppingCart, $productId, $product, $customerOrderDetail->Quantity, $productPrice, $netValue, $vatValue);
//            }
//
//        }
//    }

    public static function oneRecordCopyShoppingCartDetailToShoppingCart($id, $productId)
    {
        $shoppingCartDetailFrom = ShoppingCartDetail::find($id);

        if ( !empty($shoppingCartDetailFrom)) {

            $shoppingCart = $scc->openedShoppingCart();
            $product = Product::find($productId);
            $shoppingCartDetail = ShoppingCartDetail::where('ShoppingCart', $shoppingCart->Id)->where('Product', $productId)->first();
            $productPrice = productPriceClass::getProductPrice($productId, $shoppingCartDetailFrom->Quantity, $product->QuantityUnit, $shoppingCartDetailFrom->Currency);
            $vat = Vat::find($product->Vat);

            $netValue = ($shoppingCartDetailFrom->Quantity * $productPrice);
            $vatValue = ($shoppingCartDetailFrom->Quantity * $productPrice) * (( 100 + $vat->Rate) / 100);

            if ( empty($shoppingCart)) {
                $action = new ShoppingCartInsertGetIdAction();
                $action->handle($netValue, $vatValue);
            } else {
                $action = new ShoppingCartValueUpdate();
                $action->handle($shoppingCart, $netValue, $vatValue);
            }


            if ( empty($shoppingCartDetail) ) {
                $action = new ShoppingCartDetailInsertGetIdAction();
                $action->handle($shoppingCart, $productId, $product, 0, $productPrice, $netValue, $vatValue);
            }
        }
    }

    public static function excelImportToShoppingCartDetail(ShoppingCartOpened $scc)
    {
        $excelImports = ExcelImport::where('user_id', myUser::user()->id)->all();
        foreach ($excelImports as $excelImport) {
            $product = Product::where('Code', $excelImport->Name)
                               ->where('Deleted', 0)
                               ->first();
            if (empty($product)) {
                $product = Product::where('Id', function($query) use($excelImport){
                    return $query->from('productcustomercode')
                                 ->select('Product')
                                 ->where('Code', $excelImport->Name)
                                 ->where('Customer', myUser::user()->customerId)
                                 ->first();
                })->first();
                if (!empty($product)) {
                    $quantity     = floatval($excelImport->Quantity);
                    $productPrice = productPriceClass::getProductPrice($product->Id, $quantity, $product->QuantityUnit, -1);
                    $shoppingCart = $scc->openedShoppingCart();
                    $vat          = Vat::find($product->Vat);

                    $netValue = ($quantity * $productPrice);
                    $vatValue = ($quantity * $productPrice) * (( 100 + $vat->Rate) / 100);

                    $action = new ShoppingCartDetailInsertGetIdAction();
                    $action->handle($shoppingCart, $product->Id, $product, $quantity, $productPrice, $netValue, $vatValue);

                }
            }
        }
        return true;
    }

}

