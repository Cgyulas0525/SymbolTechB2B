<?php

namespace App\Actions\ShoppingCartDetail;

use App\Classes\logClass;
use DB;
use Carbon\Carbon;

class ShoppingCartDetailInsertGetIdAction
{
    public function handle($shoppingCart, $productId, $product, $quantity, $productPrice, $netValue, $vatValue) {
        $scdId = DB::table('ShoppingCartDetail')
            ->insertGetId([
                'ShoppingCart' => $shoppingCart->Id,
                'Currency' => -1,
                'CurrencyRate' => 1,
                'Product' => $productId,
                'Vat' => $product->Vat,
                'QuantityUnit' => $product->QuantityUnit,
                'Reverse' => 0,
                'Quantity' => $quantity,
                'UnitPrice' => $productPrice,
                'NetValue' => $netValue,
                'GrossValue' => $netValue + $vatValue,
                'VatValue' => $vatValue,
                'created_at' => Carbon::now()
            ]);
        logClass::insertDeleteRecord( 1, "ShoppingCartDetail", $scdId);
    }
}
