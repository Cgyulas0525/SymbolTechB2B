<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Classes\Api\truncateTablesClass;

class truncateTablesController extends Controller
{
    public function truncateAllTables() {
        $td = new truncateTablesClass();
        $td->truncateTables();
    }
}
