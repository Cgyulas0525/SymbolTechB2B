<?php

namespace App\Traits\ShoppingCart;

use App\Models\ShoppingCartDetail;
use Response;

trait SCDIndexTrait {
    public function sCDIndex($id)
    {
        return Response::json(ShoppingCartDetail::where('ShoppingCart', $id)->get());
    }

}
