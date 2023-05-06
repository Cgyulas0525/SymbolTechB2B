<?php

namespace App\Actions\ShoppingCartDetail;

use App\Actions\ShoppingCartDetail\OneDetailToOpenedShoppingCartAction;
use App\Models\ShoppingCartDetail;
use DB;


class AllDetailToOpenedShoppingCartAction
{

    private $action;

    public function __construct() {

        $this->action = new OneDetailToOpenedShoppingCartAction();

    }

    public function handle($id) {

        $shoppingCartDetails = ShoppingCartDetail::where('ShoppingCart', $id)->get();

        if (!empty($shoppingCartDetails)) {

            DB::beginTransaction();

            try {

                foreach ($shoppingCartDetails as $shoppingCartDetail) {

                    $this->action->handle($shoppingCartDetail->Id);
                }

                DB::commit();

            } catch (\Exception $e) {

                DB::rollBack();

                throw new \Exception($e->getMessage);
            }
        }
    }
}
