<?php
namespace App\Traits\ShoppingCart;

use App\Actions\ShoppingCartDetail\OneDetailToOpenedShoppingCartAction;

use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;
use DB;

trait SCDCopyTrait {

    public function scdCopy($id) {

        DB::beginTransaction();

        try {

            $copy = new OneDetailToOpenedShoppingCartAction();
            $copy->handle($id);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw new \Exception($e->getMessage);

        }

        return view('shopping_carts.edit')->with('shoppingCart', ShoppingCart::find(ShoppingCartDetail::find($id)->ShoppingCart));

    }
}
