<?php

namespace App\Traits\Others;

use App\Classes\logClass;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;

use DB;
use App\Classes\productPriceClass;

use myUser;

trait OneExcelImportToShoppingCartDetailTrait {

    public function oneExcelImportToShoppingCartDetail(Request $request)
    {

        // TODO nincs rendesen lekezelve! mi van ha van? Azt nem írtam meg!!!! 2023.04.25

        $product = Product::with('vatRelation')->where('Code', $request->get('code'))
            ->where('Deleted', 0)
            ->first();

        if (empty($product)) {

            $product = Product::with('vatRelation')->where('Id', function($query) use($request){
                return $query->from('productcustomercode')
                    ->select('Product')
                    ->where('Code', $request->get('code'))
                    ->where('Customer', myUser::user()->customerId)
                    ->first();
            })->first();

            if (!empty($product)) {

                $productPrice = productPriceClass::getProductPrice($product->Id, $request->get('quantity'), $product->QuantityUnit, -1);

                $scdId = DB::table('ShoppingCartDetail')
                    ->insertGetId([
                        'ShoppingCart' => ShoppingCart::OwnOpen(myUser::user()->customerId, myUser::user()->customercontact_id)->first()->Id,
                        'Currency' => -1,
                        'CurrencyRate' => 1,
                        'Product' => $product->Id,
                        'Vat' => $product->Vat,
                        'QuantityUnit' => $product->QuantityUnit,
                        'Reverse' => 0,
                        'Quantity' => $request->get('quantity'),
                        'UnitPrice' => $productPrice,
                        'NetValue' => $request->get('quantity') * $productPrice,
                        'GrossValue' => ($request->get('quantity') * $productPrice) * (( 100 + $product->vat->Rate) / 100),
                        'VatValue' => ($request->get('quantity') * $productPrice) * ($product->vat->Rate / 100),
                        'created_at' => \Carbon\Carbon::now()
                    ]);

                logClass::insertDeleteRecord( 5, "ShoppingCartDetail", $scdId);

                return 0;

            } else {

                return 1;

            }
        }

        return 1;
    }

}
