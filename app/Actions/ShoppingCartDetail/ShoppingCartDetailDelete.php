<?php

namespace App\Actions\ShoppingCartDetail;

use App\Classes\logClass;
use Carbon\Carbon;
use DB;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailObserverAction;

class ShoppingCartDetailDelete
{

    private $scdoa;

    public function __construct() {
        $this->scdoa = new ShoppingCartDetailObserverAction();
    }

    public function handle($shoppingCartDetail) {

        DB::beginTransaction();

        try {

            DB::table('shoppingcartdetail')->where('Id', $shoppingCartDetail->Id)->update(['deleted_at' => Carbon::now()]);

            $this->scdoa->handle();

            logClass::insertDeleteRecord( 7, "ShoppingCartDetail", $shoppingCartDetail->Id);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }

    }

}
