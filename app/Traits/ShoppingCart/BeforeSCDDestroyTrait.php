<?php

namespace App\Traits\ShoppingCart;

use App\Classes\langClass;
use App\Models\ShoppingCartDetail;
use Alert;

trait BeforeSCDDestroyTrait {

    public function beforeSCDDestroy($id) {

        $shoppingCartDetail = ShoppingCartDetail::with('shoppingCartRelation')
            ->with('productRelation')
            ->find($id);

        Alert::question( langClass::trans('Biztos, hogy kiakarja törölni a tételt?'))
            ->showCancelButton(
                $btnText = '<a class="swCancelButton" href="'. route('shoppingCarts.edit', [$shoppingCartDetail->ShoppingCart]) .'">' . langClass::trans('Kilép') .'</a>',
                $btnColor = '#0066cc')
            ->showConfirmButton(
                $btnText = '<a class="swConfirmButton" href="'. route('shoppingCartDetailDestroy', [$id]) .'">' . langClass::trans('Töröl') .'</a>', // here is class for link
                $btnColor = '#ff0000',
            )->autoClose(false);

        return view('shopping_cart_details.show')->with('shoppingcartdetail', $shoppingCartDetail);
    }

}
