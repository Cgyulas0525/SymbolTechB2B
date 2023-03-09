<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\Api\getSUXMLClass;

class getSUXMLController extends Controller
{
    public function dataProcess(){
        $class = new getSUXMLClass();
        $class->process();
    }
}
