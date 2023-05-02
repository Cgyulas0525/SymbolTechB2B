<?php

namespace App\Traits\ShoppingCart;

use App\Actions\ShoppingCartDetail\ShoppingCartDetailDelete;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailObserverAction;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;

use DB;
use App\Classes\logClass;

trait ShoppingCartDetailDestroyTrait {

    public function destroy($id) {

        $shoppingCartDetail = ShoppingCartDetail::with('shoppingCartRelation')->find($id);

        if (empty($shoppingCartDetail)) {
            return redirect(route('shoppingCartDetails.index'));
        }

        $shoppingCart = ShoppingCart::find($shoppingCartDetail->shoppingCartRelation->Id);

        DB::beginTransaction();

        try {

            $scd = new ShoppingCartDetailDelete();
            $scd->handle($shoppingCartDetail);

            $shoppingCartDetailObserverAction = new ShoppingCartDetailObserverAction();
            $shoppingCartDetailObserverAction->handle();

            $modifiedShoppingCart = ShoppingCart::find($shoppingCartDetail->shoppingCartRelation->Id);
            logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw new \Exception($e->getMessage);
        }

        return view('shopping_carts.edit')->with('shoppingCart', isset($modifiedShoppingCart) ? $modifiedShoppingCart : $shoppingCart);

    }

}
