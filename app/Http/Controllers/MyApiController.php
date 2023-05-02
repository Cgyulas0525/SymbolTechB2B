<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Response;
use DB;

use myUser;

use App\Classes\logClass;

use App\Models\Employee;
use App\Models\Users;
use App\Models\CustomerContact;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;
use App\Models\CustomerOrderDetail;
use App\Models\Translations;

use App\Imports\excelImportImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

use App\Services\ShoppingCartService;
use App\Services\ShoppingCartDetailService;
use App\Actions\ShoppingCartDetail\ShoppingCartDetailObserverAction;

use App\Actions\CustomerOrderDetail\OrderDetailToShoppingCartAction;
use App\Actions\ShoppingCart\OneRecordCopyShoppingCartDetailToShoppingCartAction;

use App\Traits\Others\ChangeEnvironmentVariableTrait;
use App\Traits\Others\PostCustomerPriceTrait;
use App\Traits\Others\CustomerContactDDWTrait;
use App\Traits\Others\CustomerAddressDDWTrait;
use App\Traits\Others\OneExcelImportToShoppingCartDetailTrait;
use App\Traits\Others\ChangeENVTrait;

class MyApiController extends Controller
{
    private $shoppingCartService;
    private $shoppingCartDetailService;
    private $shoppingCartDetailObserverAction;
    private $orderDetailToShoppingCartAction;
    private $oneRecordCopyShoppingCartDetailToShoppingCartAction;

    public function __construct() {
        $this->shoppingCartService = new ShoppingCartService();
        $this->shoppingCartDetailService = new ShoppingCartDetailService();
        $this->shoppingCartDetailObserverAction = new ShoppingCartDetailObserverAction();
        $this->orderDetailToShoppingCartAction = new OrderDetailToShoppingCartAction;
        $this->oneRecordCopyShoppingCartDetailToShoppingCartAction = new OneRecordCopyShoppingCartDetailToShoppingCartAction();
    }

    use ChangeEnvironmentVariableTrait, PostCustomerPriceTrait, CustomerContactDDWTrait, CustomerAddressDDWTrait,
        OneExcelImportToShoppingCartDetailTrait, ChangeENVTrait;

    /*
     * ShoppingCartDetail értékek módosítás
     *
     * @param $request
     *
     * @return ShoppingCartDetail
     */
    public function shoppingCartDetailQuantityUpdate(Request $request)
    {

        DB::beginTransaction();

        try {

            $this->shoppingCartDetailService->shoppingCartDetailValueUpdate($request);
            $this->shoppingCartDetailObserverAction->handle();

            DB::commit();

        } catch (\InvalidArgumentException $e) {

            DB::rollBack();

        }

        return Response::json(ShoppingCartDetail::find($request->get('Id')));

    }

    public function setShoppingCartDetail ( Request $request )
    {

        DB::beginTransaction();

        try {

            $this->shoppingCartDetailService->shoppingCartDetailUpdate($request);
            $this->shoppingCartDetailObserverAction->handle();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw new \Exception($e->getMessage());

        }

        return Response::json(ShoppingCart::find($request->get('Id')));
    }

    public function insertShoppingCartDetail ( Request $request)
    {

        DB::beginTransaction();

        try {

            $this->shoppingCartDetailService->shoppingCartDetailInsert($request);
            $this->shoppingCartDetailObserverAction->handle();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            throw new \Exception($e->getMessage());

        }

        return Response::json(ShoppingCart::find($request->get('Id')));

    }

    public function itemTraslation(Request $request) {

        Translations::where('id', $request->get('id'))->update(['name' => $request->get('name')]);

    }

    public function excelImportDDW(Request $request) {

        $excel = Excel::toArray(new excelImportImport, $request->get('file') );
        $firstRow = $excel[0][0];
        $values = array_values($firstRow);
        return $values;

    }


    /*
     * get Employee record with id
     *
     * @param $request
     *
     * @return Employee json
     */
    public function getEmployee(Request $request) {

        return Response::json( Employee::where('Id', $request->get('id'))->first() );

    }

    /*
     * get CustomerContact record with id
     *
     * @param $request
     *
     * @return CustomerContact json
     */
    public function getCustomerContact(Request $request) {

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
 * ShoppingCart értékek módosítás
 *
 * @param $request
 *
 * @return ShoppingCart
 */
    public function shoppingCartUpdate(Request $request)
    {

        return Response::json($this->shoppingCartService->shoppingCartUpdateValueModify($request));

    }

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


    public function copyCustomerOrderDetailToShoppingCart(Request $request) {

        DB::beginTransaction();

        try {

            $this->orderDetailToShoppingCartAction->handle($request->get('Id'), $request->get('Product'));

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }

        return true;

    }

    public function copyCustomerOrderToShoppingCart(Request $request) {

        $customerOrderDetails = CustomerOrderDetail::where('CustomerOrder', $request->get('Id'))->get();

        DB::beginTransaction();

        try {

            foreach ($customerOrderDetails as $customerOrderDetail) {

                $this->orderDetailToShoppingCartAction->handle($customerOrderDetail->Id, $customerOrderDetail->Product);

            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }

        return true;
    }

    public function copyShoppingCartToShoppingCart(Request $request)
    {

        DB::beginTransaction();

        try {

            $shoppingCartDetails = ShoppingCartDetail::where('ShoppingCart', $request->get('Id'))->get();

            foreach ($shoppingCartDetails as $shoppingCartDetail) {

                $this->oneRecordCopyShoppingCartDetailToShoppingCartAction->handle($shoppingCartDetail->Id, $shoppingCartDetail->Product);

            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

        }

        return true;
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

        $response = Http::get(url('/apik/getCurrency.php'));
//        $response = Http::get(substr(url(''), 0, strpos(url(''), 'public')).'storage/apik/getCurrency.php');
        return redirect(route('apis.index'));
    }

    public function getSUXML ( Request $request)
    {
//        $response = Http::get(substr(url(''), 0, strpos(url(''), 'public')).'storage/apik/getSUXML.php');
        $response = Http::get(url('/apik/getSUXML.php'));
//        $this->changeEnv('INSTALL_STATUS', '2');
        Artisan::call('optimize:clear');
        return back();
    }

    public function getSUXMLInstall ( Request $request)
    {
//        $response = Http::get(substr(url(''), 0, strpos(url(''), 'public')).'storage/apik/getSUXML.php');
        $response = Http::get(url('/apik/getSUXML.php'));
        $this->changeEnv('INSTALL_STATUS', '2');
        Artisan::call('optimize:clear');
        return redirect(route('home'));
    }

    public function getSUXSD ( Request $request)
    {
//        $response = Http::get(substr(url(''), 0, strpos(url(''), 'public')).'storage/apik/getSUXSD.php');
        $response = Http::get(url('/apik/getSUXSD.php'));
//        $this->changeEnv('INSTALL_STATUS', '1');
        Artisan::call('optimize:clear');
        return back();
    }

    public function getSUXSDInstall ( Request $request)
    {
//        $response = Http::get(substr(url(''), 0, strpos(url(''), 'public')).'storage/apik/getSUXSD.php');
        $response = Http::get(url('/apik/getSUXSD.php'));
        $this->changeEnv('INSTALL_STATUS', '1');
        Artisan::call('optimize:clear');
        return redirect(route('home'));
    }

    public static function SendShoppingCart(Request $request)
    {
        $response = Http::get(substr(url(''), 0, strpos(url(''), 'public')).'storage/apik/SendShoppingCart.php');
        return redirect(route('apis.index'));
    }

}

