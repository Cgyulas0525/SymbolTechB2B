<?php

use Illuminate\Support\Facades\Route;
use App\Classes\shoppingCartClass;
use App\Http\Controllers\DashboardController;

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

Route::get('class/getSCDs', [shoppingCartClass::class, 'getSCDs']);

Route::get('CustomerOrderInterval/{from}/{to}', [DashboardController::class, 'CustomerOrderInterval'])->name('CustomerOrderInterval');

