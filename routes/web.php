<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyloginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DivisionExportController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerOfferController;
use App\Http\Controllers\LogItemController;
use App\Http\Controllers\ShoppingCartDetailController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\CustomerContactFavoriteProductController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\TranslationsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ApimodelController;
use App\Http\Controllers\ApimodelerrorController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [
    HomeController::class, 'index'
])->name('home');

Route::post('login', [MyloginController::class, 'login'])->name('myLogin');
Route::get('settingIndex', [SettingController::class, 'index'])->name('settingIndex');
Route::get('communicationIndex', [SettingController::class, 'communicationIndex'])->name('communicationIndex');

Route::get('export/{table}', [DivisionExportController::class, 'export'])->name('DivisionExport');
Route::get('XMLImport', [DivisionExportController::class, 'XMLImport'])->name('XMLImport');

Route::resource('customerOrders', App\Http\Controllers\CustomerOrderController::class);
Route::get('index/{id}', [CustomerOrderController::class, 'index'])->name('customerOrderIndex');

Route::resource('users', App\Http\Controllers\UsersController::class);
Route::get('B2BCustomerUserIndex', [UsersController::class, 'B2BCustomerUserIndex'])->name('B2BCustomerUserIndex');
Route::get('B2BCustomerIndex', [UsersController::class, 'B2BCustomerIndex'])->name('B2BCustomerIndex');
Route::get('B2BCustomerUserCreate', [UsersController::class, 'B2BCustomerUserCreate'])->name('B2BCustomerUserCreate');
Route::get('B2BUserIndex', [UsersController::class, 'B2BUserIndex'])->name('B2BUserIndex');
Route::get('belsoUserDestroy/{id}', [UsersController::class, 'belsoUserDestroy'])->name('belsoUserDestroy');
Route::get('B2BUserDestroy/{id}', [UsersController::class, 'B2BUserDestroy'])->name('B2BUserDestroy');
Route::get('profil/{id}', [UsersController::class, 'profil'])->name('profil');

Route::get('B2BCustomerLoginCountIndex', [AdminController::class, 'B2BCustomerLoginCountIndex'])->name('B2BCustomerLoginCountIndex');

Route::get('oneOffer/{id}', [CustomerOfferController::class, 'oneOffer'])->name('oneOffer');
Route::get('customerOfferDetailIndex/{id}', [CustomerOfferController::class, 'customerOfferDetailIndex'])->name('customerOfferDetailIndex');

Route::get('index', [CustomerController::class, 'index'])->name('customerIndex');
Route::get('dIndex', [CustomerController::class, 'dIndex'])->name('dIndex');

Route::resource('logItems', LogItemController::class);
Route::get('indexBetween/{startDate}/{endDate}/{customer}/{user}', [LogItemController::class, 'indexBetween'])->name('indexBetween');
Route::get('indexLogItemTableDetail/{id}', [LogItemController::class, 'indexLogItemTableDetail'])->name('indexLogItemTableDetail');
Route::get('logItemTableDetailIndex/{id}', [LogItemController::class, 'logItemTableDetailIndex'])->name('logItemTableDetailIndex');

Route::resource('shoppingCarts', App\Http\Controllers\ShoppingCartController::class);
Route::get('editShoppingCart', [ShoppingCartController::class, 'editShoppingCart'])->name('editShoppingCart');
Route::get('cartDestroy/{id}', [ShoppingCartController::class, 'cartDestroy'])->name('cartDestroy');
Route::get('shoppingCartDetailIndex/{id}', [ShoppingCartController::class, 'shoppingCartDetailIndex'])->name('shoppingCartDetailIndex');
Route::get('sCDIndex/{id}', [ShoppingCartController::class, 'sCDIndex'])->name('sCDIndex');
Route::get('close/{id}', [ShoppingCartController::class, 'close'])->name('shoppingCartClose');
Route::get('open/{id}', [ShoppingCartController::class, 'open'])->name('shoppingCartOpen');

Route::resource('shoppingCartDetails', App\Http\Controllers\ShoppingCartDetailController::class);
//Route::get('index/{id}', [ShoppingCartDetailController::class, 'index'])->name('shoppingCartDetailIndex');
Route::get('create/{id}', [ShoppingCartDetailController::class, 'create'])->name('shoppingCartDetailCreate');
Route::get('destroy/{id}', [ShoppingCartDetailController::class, 'destroy'])->name('shoppingCartDetailDestroy');
Route::get('productIndex', [ShoppingCartDetailController::class, 'productIndex'])->name('productIndex');
Route::get('customerOfferProductIndex', [ShoppingCartDetailController::class, 'customerOfferProductIndex'])->name('customerOfferProductIndex');
Route::get('customerContractProductIndex', [ShoppingCartDetailController::class, 'customerContractProductIndex'])->name('customerContractProductIndex');
Route::get('favoriteProductIndex', [ShoppingCartDetailController::class, 'favoriteProductIndex'])->name('favoriteProductIndex');

Route::get('customerOrderDetailIndex/{id}', [CustomerOrderController::class, 'customerOrderDetailIndex'])->name('customerOrderDetailIndex');
Route::get('indexAllThisYear', [CustomerOrderController::class, 'indexAllThisYear'])->name('indexAllThisYear');
Route::get('indexOwn', [CustomerOrderController::class, 'indexOwn'])->name('indexOwn');
Route::get('indexYearAllOwn', [CustomerOrderController::class, 'indexYearAllOwn'])->name('indexYearAllOwn');
Route::get('indexSC', [CustomerOrderController::class, 'indexSC'])->name('indexSC');
Route::get('indexSCThisYear', [CustomerOrderController::class, 'indexSCThisYear'])->name('indexSCThisYear');
Route::get('indexCOLastTreeMonth', [CustomerOrderController::class, 'indexCOLastTreeMonth'])->name('indexCOLastTreeMonth');

Route::post('importExcel', [ShoppingCartController::class, 'importExcel'])->name('importExcel');
Route::get('excelImport', [ShoppingCartController::class, 'excelImport'])->name('excelImport');
Route::post('excelBetolt', [ShoppingCartController::class, 'excelBetolt'])->name('excelBetolt');
Route::get('excelIndex', [ShoppingCartController::class, 'excelIndex'])->name('excelIndex');
Route::get('excelImportUseRecordsDelete', [ShoppingCartController::class, 'excelImportUseRecordsDelete'])->name('excelImportUseRecordsDelete');

Route::resource('customerContactFavoriteProducts', App\Http\Controllers\CustomerContactFavoriteProductController::class);
Route::get('productCategoryProductindex/{category}', [CustomerContactFavoriteProductController::class, 'productCategoryProductindex'])->name('productCategoryProductindex');
Route::get('destroyMe/{id}', [CustomerContactFavoriteProductController::class, 'destroyMe'])->name('cCFPDestroyMe');

Route::get('lan-change', [LanguageController::class, 'langChange'])->name('lan.change');

Route::resource('languages', App\Http\Controllers\LanguagesController::class);

Route::get('languageOn/{id}', [LanguagesController::class, 'languageOn'])->name('languageOn');
Route::get('languageOff/{id}', [LanguagesController::class, 'languageOff'])->name('languageOff');
Route::get('beforeOn/{id}', [LanguagesController::class, 'beforeOn'])->name('beforeOn');
Route::get('beforeOff/{id}', [LanguagesController::class, 'beforeOff'])->name('beforeOff');

Route::resource('translations', App\Http\Controllers\TranslationsController::class);
Route::get('indexLanguage/{language}', [TranslationsController::class, 'indexLanguage'])->name('indexLanguage');

Route::resource('apis', App\Http\Controllers\ApiController::class);
Route::resource('apimodels', App\Http\Controllers\ApimodelController::class);
Route::resource('apimodelerrors', App\Http\Controllers\ApimodelerrorController::class);

Route::get('index/{id}', [ApimodelController::class, 'index'])->name('indexApimodel');
Route::get('indexApimodelerror/{id}', [ApimodelerrorController::class, 'indexApimodelerror'])->name('indexApimodelerror');


