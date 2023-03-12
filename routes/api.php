<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\getCurrencyController;
use App\Http\Controllers\Api\outputFilesDeleteController;
use App\Http\Controllers\Api\getSUXSDController;
use App\Http\Controllers\Api\getSUXMLController;
use App\Http\Controllers\Api\truncateTablesController;
use App\Http\Controllers\Api\sendShoppingCartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('importCurrency', [getCurrencyController::class, 'importCurrency']);
Route::get('deleteOutputFiles', [outputFilesDeleteController::class, 'deleteOutputFiles']);
Route::get('structureProcess', [getSUXSDController::class, 'structureProcess']);
Route::get('dataProcess', [getSUXMLController::class, 'dataProcess']);
Route::get('truncateAllTables', [truncateTablesController::class, 'truncateAllTables']);
Route::get('sendCart', [sendShoppingCartController::class, 'sendCart']);
