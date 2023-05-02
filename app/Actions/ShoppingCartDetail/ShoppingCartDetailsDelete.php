<?php

namespace App\Actions\ShoppingCartDetail;

use App\Classes\logClass;
use App\Models\ShoppingCartDetail;
use Carbon\Carbon;
use DB;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailObserverAction;

class ShoppingCartDetailsDelete
{
    private $scdoa;
    private $id;

    public function __construct($id) {
        $this->scdoa = new ShoppingCartDetailObserverAction();
        $this->id = $id;
    }

    public function handle() {

        $details = ShoppingCartDetail::where('ShoppingCart', $this->id)->get();

        if (!empty($details)) {

            foreach ( $details as $detail ) {

                DB::table('shoppingcartdetail')->where('Id', $detail->Id)->update(['deleted_at' => Carbon::now()]);

                logClass::insertDeleteRecord( 7, "ShoppingCartDetail", $detail->Id);

            }

            $this->scdoa->handle();

        }



    }
}
