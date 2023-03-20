<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\Api\getSUXSDClass;

class getSUXSDController extends Controller
{
    public function structureProcess() {
        $su = new getSUXSDClass();
        $su->process();

        return redirect(route('login'));
    }
}
