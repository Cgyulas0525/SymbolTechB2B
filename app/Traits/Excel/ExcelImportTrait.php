<?php

namespace App\Traits\Excel;

use App\Classes\ShoppingCart\ShoppingCartOpened;

trait ExcelImportTrait {
    public function excelImport(ShoppingCartOpened $scc)
    {
        $shoppingCart = $scc->openedShoppingCart();
        return view('shopping_carts.excelImport')->with('shoppingCart', $shoppingCart);
    }

}
