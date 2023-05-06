<?php

namespace App\Traits\ShoppingCart;

use App\Classes\langClass;
use Alert;
use App\Models\ShoppingCart;
use myUser;

trait BeforeAllSCDCopyTrait {

    public function beforeAllSCDCopy($id) {

        Alert::question( langClass::trans('Biztos, hogy át akarja másolni a kosár összes tételét?'))
            ->showCancelButton(
                $btnText = '<a class="swCancelButton" href="'. route('shoppingCartIndex', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                        'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]) .'">' . langClass::trans('Kilép') .'</a>',
                $btnColor = '#0066cc')
            ->showConfirmButton(
                $btnText = '<a class="swConfirmButton" href="'. route('scdAllCopy', [$id]) .'">' . langClass::trans('Másol') .'</a>', // here is class for link
                $btnColor = '#ff0000',
            )->autoClose(false);

        return view('shopping_carts.showCopy')->with('shoppingcart', ShoppingCart::find($id));

    }

}
