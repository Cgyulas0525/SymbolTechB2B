<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('api/getUserWithEmployeeId', [MyApiController::class, 'getUserWithEmployeeId']);
Route::get('api/getEmployee', [MyApiController::class, 'getEmployee']);
Route::get('api/getCustomerContact', [MyApiController::class, 'getCustomerContact']);
Route::get('api/passwordChange', [MyApiController::class, 'passwordChange']);
Route::get('api/customerContactDDW', [MyApiController::class, 'customerContactDDW']);
Route::get('api/customerAddressDDW', [MyApiController::class, 'customerAddressDDW']);
Route::get('api/changeEnvironmentVariable', [MyApiController::class, 'changeEnvironmentVariable']);
Route::get('api/logItemUserDDW', [MyApiController::class, 'logItemUserDDW']);
Route::get('api/shoppingCartDetailQuantityUpdate', [MyApiController::class, 'shoppingCartDetailQuantityUpdate']);
Route::get('api/shoppingCartUpdate', [MyApiController::class, 'shoppingCartUpdate']);
Route::get('api/getShoppingCartDetail', [MyApiController::class, 'getShoppingCartDetail']);
Route::get('api/getShoppingCart', [MyApiController::class, 'getShoppingCart']);
Route::get('api/setShoppingCartDetail', [MyApiController::class, 'setShoppingCartDetail']);
Route::get('api/insertShoppingCartDetail', [MyApiController::class, 'insertShoppingCartDetail']);
Route::get('api/copyCustomerOrderDetailToShoppingCart', [MyApiController::class, 'copyCustomerOrderDetailToShoppingCart']);
Route::get('api/copyCustomerOrderToShoppingCart', [MyApiController::class, 'copyCustomerOrderToShoppingCart']);
Route::get('api/copyShoppingCartToShoppingCart', [MyApiController::class, 'copyShoppingCartToShoppingCart']);
Route::get('api/excelImportDDW', [MyApiController::class, 'excelImportDDW'])->name('excelImportDDW');
Route::get('api/oneExcelImportToShoppingCartDetail', [MyApiController::class, 'oneExcelImportToShoppingCartDetail'])->name('oneExcelImportToShoppingCartDetail');
Route::get('api/excelImportTruncate', [MyApiController::class, 'excelImportTruncate'])->name('excelImportTruncate');
Route::get('api/excelImportIdDelete', [MyApiController::class, 'excelImportIdDelete'])->name('excelImportIdDelete');
Route::post('api/datatableSave', [MyApiController::class, 'datatableSave'])->name('datatableSave');
Route::post('api/datatableLoad', [MyApiController::class, 'datatableLoad'])->name('datatableLoad');
Route::get('api/makeCustomerContactFavoriteProduct', [MyApiController::class, 'makeCustomerContactFavoriteProduct'])->name('makeCustomerContactFavoriteProduct');
Route::get('api/itemTraslation', [MyApiController::class, 'itemTraslation'])->name('itemTraslation');
Route::get('api/getCurrencyRate', [MyApiController::class, 'getCurrencyRate'])->name('getCurrencyRate');
Route::get('api/SendShoppingCart', [MyApiController::class, 'SendShoppingCart'])->name('SendShoppingCart');
Route::get('api/getSUXML', [MyApiController::class, 'getSUXML'])->name('getSUXML');
Route::get('api/postCustomerPrice', [MyApiController::class, 'postCustomerPrice'])->name('postCustomerPrice');


