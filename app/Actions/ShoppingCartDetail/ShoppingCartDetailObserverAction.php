<?php

namespace App\Actions\ShoppingCartDetail;

use App\Models\ShoppingCart;
use \Carbon\Carbon;
use myUser;

class ShoppingCartDetailObserverAction
{
    public function handle() {

        $item = ShoppingCart::OwnOpen(myUser::user()->customerId, myUser::user()->customercontact_id)->with('shoppingCartDetailRelation')->first();

        ShoppingCart::UpdateOrInsert(
            ['Id' => $item->Id],
            ['NetValue' => $item->shoppingCartDetailRelation->sum('NetValue'),
             'GrossValue' => $item->shoppingCartDetailRelation->sum('GrossValue'),
             'VatValue' => $item->shoppingCartDetailRelation->sum('VatValue'),
             'updated_at' => Carbon::now()
            ]
        );

    }
}
