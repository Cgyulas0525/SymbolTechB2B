<?php

namespace App\Services;

use App\Models\ShoppingCartDetail;
use Carbon\Carbon;
use App\Classes\logClass;

class ShoppingCartDetailService
{
    public function shoppingCartDetailDelete($shoppingCartDetail) {

        $shoppingCartDetail->deleted_at = Carbon::now();
        $shoppingCartDetail->save();

    }

    public function shoppingCartDetailsDelete($id) {
        $details = ShoppingCartDetail::where('ShoppingCart', $id)->get();

        foreach ( $details as $detail ) {
            $this->shoppingCartDetailDelete($detail);
            logClass::insertDeleteRecord( 5, "ShoppingCartDetail", $detail->Id);
        }

    }
}
