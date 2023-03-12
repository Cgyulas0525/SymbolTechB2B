<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\Api\sendShoppingCartClass;

class sendShoppingCartController extends Controller
{
    public function sendCart() {
        $sc = new sendShoppingCartClass();
        $sc->sendShoppingCart();
    }
}
