<?php

namespace App\Http\Controllers;

use App\Traits\Destroy\BeforeDestroyTrait;
use App\Traits\Destroy\BeforeDestroysWithParamArrayTrait;
use App\Traits\Destroy\DestroyTrait;
use App\Traits\Destroy\DestroyWithParamTrait;

class DestroysController extends Controller
{

    use BeforeDestroyTrait, BeforeDestroysWithParamArrayTrait, DestroyTrait, DestroyWithParamTrait;

}

