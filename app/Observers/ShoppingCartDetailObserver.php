<?php

namespace App\Observers;

use App\Models\ShoppingCartDetail;

class ShoppingCartDetailObserver
{
    /**
     * Handle the ShoppingCartDetail "created" event.
     *
     * @param  \App\Models\ShoppingCartDetail  $shoppingCartDetail
     * @return void
     */
    public function created(ShoppingCartDetail $shoppingCartDetail)
    {
        //
    }

    /**
     * Handle the ShoppingCartDetail "updated" event.
     *
     * @param  \App\Models\ShoppingCartDetail  $shoppingCartDetail
     * @return void
     */
    public function updated(ShoppingCartDetail $shoppingCartDetail)
    {
        //
    }

    /**
     * Handle the ShoppingCartDetail "deleted" event.
     *
     * @param  \App\Models\ShoppingCartDetail  $shoppingCartDetail
     * @return void
     */
    public function deleted(ShoppingCartDetail $shoppingCartDetail)
    {
        //
    }

    /**
     * Handle the ShoppingCartDetail "restored" event.
     *
     * @param  \App\Models\ShoppingCartDetail  $shoppingCartDetail
     * @return void
     */
    public function restored(ShoppingCartDetail $shoppingCartDetail)
    {
        //
    }

    /**
     * Handle the ShoppingCartDetail "force deleted" event.
     *
     * @param  \App\Models\ShoppingCartDetail  $shoppingCartDetail
     * @return void
     */
    public function forceDeleted(ShoppingCartDetail $shoppingCartDetail)
    {
        //
    }
}
