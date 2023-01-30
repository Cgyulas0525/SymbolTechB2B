<?php

namespace App\Http\Controllers;

use App\Models\ExcelImport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Response;
use DB;
use Flash;

use myUser;
use shoppingCartClass;
use productPriceClass;
use logClass;
use App\Classes\myFtp;

use App\Models\Employee;
use App\Models\Users;
use App\Models\CustomerContact;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;
use App\Models\Product;
use App\Models\Vat;
use App\Models\CustomerOrderDetail;
use App\Models\Translations;
use App\Models\Customer;

use App\Imports\excelImportImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;


class MyApiController extends Controller
{

    public function itemTraslation(Request $request)
    {
        Translations::where('id', $request->get('id'))->update(['name' => $request->get('name')]);
    }

    public function excelImportDDW(Request $request)
    {

        $excel = Excel::toArray(new excelImportImport, $request->get('file') );
        $firstRow = $excel[0][0];
        $values = array_values($firstRow);
        return $values;

    }

    public function changeEnvironmentVariable(Request $request)
    {
        $path = base_path('.env');
        $key = $request->get('key');
        $value = $request->get('value');

        $file = file_get_contents($path);
        if(is_bool(env($key)))
        {
            $old = env($key) ? 'true' : 'false';
        }
        elseif(env($key)===null){
            $old = 'null';
        }
        else{
            $old = env($key);
        }

        if (strlen($value) == 0) {
            $value = 'null';
        }

        $hol = intval(strpos($file, $key."='".$old. "'"));
        if ( $hol > 0) {
            $mit  = $key."='".$old. "'";
            $mire = $key."='".$value. "'";
        } else {
            $mit  = $key."=".$old;
            $mire = $key."=".$value;
        }

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $mit, $mire, $file
            ));
        }

        Artisan::call('optimize:clear');
    }

    /*
     * get Employee record with id
     *
     * @param $request
     *
     * @return Employee json
     */
    public function getEmployee(Request $request)
    {
        return Response::json( Employee::where('Id', $request->get('id'))->first() );
    }

    /*
     * get CustomerContact record with id
     *
     * @param $request
     *
     * @return CustomerContact json
     */
    public function getCustomerContact(Request $request)
    {
        return Response::json( CustomerContact::where('Id', $request->get('id'))->first() );
    }

    /*
     * get User record with employee id
     *
     * @param $request
     *
     * @return User json
     */
    public function getUserWithEmployeeId(Request $request)
    {
        return Response::json( Users::where('employee_id', $request->get('id'))->first() );
    }

    /*
     * get CustomerContact record with employee id
     *
     * @param $request
     *
     * @return User json
     */
    public function getUserWithCustomerContactId(Request $request)
    {
        return Response::json( Users::where('customercontact_id', $request->get('id'))->first() );
    }

    /*
     * User jelszó csere
     *
     * @param $request
     *
     * @return none
     */
    public function passwordChange(Request $request)
    {
        $user = Users::find(myUser::user()->id);
        $user->password = md5($request->get('password'));
        $user->save();

        return redirect(route('dIndex'));
    }

    /*
     * Customer kontaktjai DDW
     *
     * @param $request
     *
     * @return array
     */
    public static function customerContactDDW(Request $request) {
        return CustomerContact::where('Deleted', 0)
            ->where('Customer', $request->get('customer'))
            ->whereNotIn('Id', function ($query) {
                return $query->from('users')->select('customercontact_id')->whereNotNull('customercontact_id')->whereNull('deleted_at')->get();
            })
            ->select('Name', 'Id')
            ->orderBy('Name')->get();
    }

    /*
     * Customer telephelyei DDW
     *
     * @param $request
     *
     * @return array
     */
    public static function customerAddressDDW(Request $request) {
        return DB::table('CustomerAddress')
            ->selectRaw('Concat(Name, " - " , Country, " ", Zip, ". ", City, " ", Street, " ", HouseNumber) as Name, Id' )
            ->where('Customer', intval($request->get('customer')))
            ->where('Deleted', 0)
            ->orderBy('Name')->get();
    }

    /*
     * A Log táblában fellelhető userek
     *
     * @return array
     */
    public static function logItemUserDDW(Request $request) {
        return Users::whereIn('id', function ($query) use ($request){
            return $query->from('logitem')->select('user_id')->where('customer_id', intval($request->get('customer')))->groupBy('user_id')->get();
        })
            ->select( 'name','id')
            ->orderBy('name')->get();
    }

    /*
     * ShoppingCartDetail értékek módosítás
     *
     * @param $request
     *
     * @return ShoppingCartDetail
     */
    public function shoppingCartDetailQuantityUpdate(Request $request)
    {

        DB::table('ShoppingCartDetail')
            ->where('Id', $request->get('Id'))
            ->update([
                'Quantity'         => $request->get('Quantity'),
                'NetValue'         => $request->get('NetValue'),
                'VatValue'         => $request->get('VatValue'),
                'GrossValue'       => $request->get('GrossValue'),
                'updated_at'       => \Carbon\Carbon::now()
            ]);

        return Response::json( ShoppingCartDetail::find($request->get('Id')) );

    }

    /*
 * ShoppingCart értékek módosítás
 *
 * @param $request
 *
 * @return ShoppingCart
 */
    public function shoppingCartUpdate(Request $request)
    {

        DB::table('ShoppingCart')
            ->where('Id', $request->get('Id'))
            ->update([
                'NetValue'         => $request->get('NetValue'),
                'VatValue'         => $request->get('VatValue'),
                'GrossValue'       => $request->get('GrossValue'),
                'updated_at'       => \Carbon\Carbon::now()
            ]);

        return Response::json( ShoppingCart::find($request->get('Id')) );

    }

//    public function manageProductCategory()
//    {
//        $productCategories = ProductCategory::whereNull('Parent')->get();
//        $allProductCategories = ProductCategory::pluck('Name', 'Id')->all();
//        return view('categoryTreeview', compact('productCategories', 'allProductCategories'));
//    }

    public function getShoppingCartDetail ( Request $request)
    {
        return Response::json( ShoppingCartDetail::where('ShoppingCart', $request->get('Id'))
            ->where('Product', $request->get('Product'))
            ->whereNull('deleted_at')
            ->first());
    }

    public function getShoppingCart ( Request $request)
    {
        return Response::json( ShoppingCart::where('Id', $request->get('Id'))
            ->whereNull('deleted_at')
            ->first());
    }

    public function setShoppingCartDetail ( Request $request )
    {

        $shoppingCart          = ShoppingCart::find($request->get('Id'));
        $product               = Product::find($request->get('Product'));
        $shoppingCartDetail    = ShoppingCartDetail::where('ShoppingCart', $shoppingCart->Id)->where('Product', $product->Id)->whereNull('deleted_at')->first();
        $vat                   = Vat::where( 'Id', $shoppingCartDetail->Vat)->where('Deleted', 0)->first();
        $quantity              = $shoppingCartDetail->Quantity + $request->get('Quantity');
        DB::table('ShoppingCartDetail')
            ->where('Id', $shoppingCartDetail->Id)
            ->update([
                'Quantity'   => $quantity,
                'NetValue'   => $quantity * $shoppingCartDetail->UnitPrice,
                'VatValue'   => $quantity * ( $shoppingCartDetail->UnitPrice * ( $vat->Rate / 100 )),
                'GrossValue' => ( $quantity * $shoppingCartDetail->UnitPrice ) + ( $quantity * ( $shoppingCartDetail->UnitPrice * ( $vat->Rate / 100 ))),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        $newShoppingCartDetail = ShoppingCartDetail::find($shoppingCartDetail->Id);

        DB::table('ShoppingCart')
            ->where('Id', $shoppingCart->Id)
            ->update([
                'NetValue'   => ( $shoppingCart->NetValue - $shoppingCartDetail->NetValue ) + $newShoppingCartDetail->NetValue,
                'VatValue'   => ( $shoppingCart->VatValue - $shoppingCartDetail->VatValue ) + $newShoppingCartDetail->VatValue,
                'GrossValue' => ( $shoppingCart->GrossValue - $shoppingCartDetail->GrossValue ) + $newShoppingCartDetail->GrossValue,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        return Response::json(ShoppingCart::find($request->get('Id')));
    }

    public function insertShoppingCartDetail ( Request $request)
    {

        $product      = Product::find($request->get('Product'));
        $quantity     = $request->get('Quantity');
        $productPrice = productPriceClass::getProductPrice($product->Id, $quantity, $product->QuantityUnit, -1);
        $vat          = Vat::where( 'Id', $product->Vat)->where('Deleted', 0)->first();

        $netValue = $quantity * $productPrice;
        $vatValue = $netValue * ( $vat->Rate / 100 );

        $newShoppingCartDetail = DB::table('ShoppingCartDetail')
            ->insertGetId([
                'ShoppingCart' => $request->get('Id'),
                'Currency'     => -1,
                'CurrencyRate' => 1,
                'Product'      => $request->get('Product'),
                'Vat'          => $product->Vat,
                'QuantityUnit' => $product->QuantityUnit,
                'Quantity'     => $quantity,
                'UnitPrice'    => $productPrice,
                'NetValue'     => $netValue,
                'VatValue'     => $vatValue,
                'GrossValue'   => $netValue + $vatValue,
                'created_at'   => \Carbon\Carbon::now()
            ]);


        $newShoppingCartDetail = ShoppingCartDetail::find($newShoppingCartDetail);
        $shoppingCart          = ShoppingCart::find($request->get('Id'));

        DB::table('ShoppingCart')
            ->where('Id', $shoppingCart->Id)
            ->update([
                'NetValue'   => $shoppingCart->NetValue + $newShoppingCartDetail->NetValue,
                'VatValue'   => $shoppingCart->VatValue + $newShoppingCartDetail->VatValue,
                'GrossValue' => $shoppingCart->GrossValue + $newShoppingCartDetail->GrossValue,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        return Response::json(ShoppingCart::find($request->get('Id')));

    }

    public function copyCustomerOrderDetailToShoppingCart(Request $request)
    {
        shoppingCartClass::oneRecorCopyCustomerOrderDetailToShoppingCart($request->get('Id'), $request->get('Product'));
        return true;
    }

    public function copyCustomerOrderToShoppingCart(Request $request)
    {
        $customerOrderDetails = CustomerOrderDetail::where('CustomerOrder', $request->get('Id'))->get();
        foreach ($customerOrderDetails as $customerOrderDetail) {
            shoppingCartClass::oneRecorCopyCustomerOrderDetailToShoppingCart($customerOrderDetail->Id, $customerOrderDetail->Product);
        }
        return true;
    }

    public function copyShoppingCartToShoppingCart(Request $request)
    {
        $shoppingCartDetails = ShoppingCartDetail::where('ShoppingCart', $request->get('Id'))->get();
        foreach ($shoppingCartDetails as $shoppingCartDetail) {
            shoppingCartClass::oneRecordCopyShoppingCartDetailToShoppingCart($shoppingCartDetail->Id, $shoppingCartDetail->Product);
        }
        return true;
    }

    public function oneExcelImportToShoppingCartDetail(Request $request)
    {
        $product = Product::where('Code', $request->get('code'))
            ->where('Deleted', 0)
            ->first();
        if (empty($product)) {
            $product = Product::where('Id', function($query) use($request){
                return $query->from('productcustomercode')
                    ->select('Product')
                    ->where('Code', $request->get('code'))
                    ->where('Customer', myUser::user()->customerId)
                    ->first();
            })->first();
            if (!empty($product)) {
                $quantity     = floatval($request->get('quantity'));
                $productPrice = productPriceClass::getProductPrice($product->Id, $quantity, $product->QuantityUnit, -1);
                $shoppingCart = shoppingCartClass::openedShoppingCart(myUser::user()->customercontact_id);
                $vat          = Vat::find($product->Vat);

                $netValue = $quantity * $productPrice;
                $vatValue = $netValue * (( 100 + $vat->Rate) / 100);

                $scdId = DB::table('ShoppingCartDetail')
                    ->insertGetId([
                        'ShoppingCart' => $shoppingCart->Id,
                        'Currency' => -1,
                        'CurrencyRate' => 1,
                        'Product' => $product->Id,
                        'Vat' => $product->Vat,
                        'QuantityUnit' => $product->QuantityUnit,
                        'Reverse' => 0,
                        'Quantity' => $quantity,
                        'UnitPrice' => $productPrice,
                        'NetValue' => $netValue,
                        'GrossValue' => $netValue + $vatValue,
                        'VatValue' => $vatValue,
                        'created_at' => \Carbon\Carbon::now()
                    ]);
                logClass::insertDeleteRecord( 1, "ShoppingCartDetail", $scdId);
                return 0;
            } else {
                return 1;
            }
        }

    }

    public function excelImportTruncate(Request $request)
    {
        DB::table('excelimport')->where('user_id', myUser::user()->id)->delete();
        return true;
    }

    public function excelImportIdDelete(Request $request)
    {
        DB::table('excelimport')->where('id', $request->get('id'))->delete();
        return true;
    }

    public function datatableSave(Request $request)
    {
        DB::table('datatables_states')
            ->updateOrInsert(
                ['user_id' => myUser::user()->id, 'name' => $request->get('name')],
                ['state' => json_encode($request->get('state')),
                    'array' => json_encode($request->get('array'))
                ]
            );
    }

    public function datatableLoad(Request $request)
    {
        $ds = Response::json(DB::table('datatables_states')
            ->select('array')
            ->where('name', $request->get('name'))
            ->where('user_id', myUser::user()->id)
            ->first());
        return !empty($ds) ? $ds : null;
    }

    public function makeCustomerContactFavoriteProduct(Request $request)
    {
        $scdId = DB::table('customercontactfavoriteproduct')
            ->insertGetId([
                'product_id'         => $request->get('product'),
                'customercontact_id' => $request->get('customerContact'),
                'created_at' => \Carbon\Carbon::now()
            ]);
        logClass::insertDeleteRecord( 1, "CustomerContactFavoriteProduct", $scdId);

    }

    public function getCurrencyRate ( Request $request)
    {
        $response = Http::get(url('/public/apis/getCurrency.php'));
        return back();
    }

    public function getSUXML ( Request $request)
    {
        $response = Http::get(url('/public/apis/getSUXML.php'));
        return back();
    }

    public static function SendShoppingCart(Request $request)
    {
        $response = Http::get('/public/apis/SendShoppingCart.php');
        return back();
    }

    public function postCustomerPrice(Request $request)
    {
        if ($request->has('customer')) {
            $datas = Response::json(DB::table('product as t1')
                ->select('t1.Name', DB::raw('getProductPrice('.$request->customer.', t1.Id, 1, -1, -1) as productprice' ), DB::raw('getProductCustomerCode('.$request->customer.', t1.Id) as code' ))
                ->where('t1.Service', 0)
                ->where('t1.Deleted', 0)
                ->where('t1.Id', '<', 100)
                ->get());

            $customer = Customer::where('Id', $request->customer)->first();

            $fname = "customerprice_" . $customer->Name .".json";
            $file = getenv('FILE_UPLOAD').'/' .$fname;
            file_put_contents($file, $datas);

            if (!empty(getenv('FTP_HOST'))) {
                $myFtp = new myFtp($file, $fname);
                $myFtp->uploadFile();
            }
        }

        return view('home');
    }

}

