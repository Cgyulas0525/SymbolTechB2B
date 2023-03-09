<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\Api\fileDeleteClass;

class outputFilesDeleteController extends Controller
{
    public function deleteOutputFiles() {
        $fd = new fileDeleteClass();
        $fd->deleteOutputFiles();
    }
}
