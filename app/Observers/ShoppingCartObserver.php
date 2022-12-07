<?php

namespace App\Observers;

use App\Models\ShoppingCart;

class ShoppingCartObserver
{
    /**
     * Handle the ShoppingCart "created" event.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return void
     */
    public function created(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Handle the ShoppingCart "updated" event.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return void
     */
    public function updated(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Handle the ShoppingCart "deleted" event.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return void
     */
    public function deleted(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Handle the ShoppingCart "restored" event.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return void
     */
    public function restored(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Handle the ShoppingCart "force deleted" event.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return void
     */
    public function forceDeleted(ShoppingCart $shoppingCart)
    {
        //
    }
}
