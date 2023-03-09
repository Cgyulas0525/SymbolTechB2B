<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\Api\getCurrencyClass;

class getCurrencyController extends Controller
{
    public function importCurrency()
    {
        $gc = new getCurrencyClass('mnb');
        $gc->getArray();
    }
}
