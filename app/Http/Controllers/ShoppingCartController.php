<?php

namespace App\Http\Controllers;

use App\Classes\langClass;
use App\Http\Requests\CreateShoppingCartRequest;
use App\Http\Requests\UpdateShoppingCartRequest;
use App\Imports\excelImportImport;
use App\Repositories\ShoppingCartRepository;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Maatwebsite\Excel\Facades\Excel;
use Response;

use DB;
use DataTables;
use myUser;
use utilityClass;
use logClass;
use shoppingCartClass;

use App\Models\ShoppingCart;
use App\Models\ShoppingCartDetail;
use App\Models\ExcelImport;

use App\Classes\ShoppingCart\ShoppingCartOpened;


class ShoppingCartController extends AppBaseController
{
    /** @var  ShoppingCartRepository */
    private $shoppingCartRepository;

    public function __construct(ShoppingCartRepository $shoppingCartRepo)
    {
        $this->shoppingCartRepository = $shoppingCartRepo;
    }

    public function excelIndex(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = ExcelImport::where('user_id', myUser::user()->id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }

            return back();
        }
    }


    public function excelBetolt(Request $request)
    {
        $file = $request->file('import_file');

        if (empty($request->import_file)) {
            Flash::error(langClass::trans('Nem adott meg filet!'))->important();
        } else {

            DB::table('excelimport')->where('user_id', myUser::user()->id)->delete();
            Excel::import(new excelImportImport, $request->import_file);

        }

        return back();
    }

    public function excelImport(ShoppingCartOpened $scc)
    {
        $shoppingCart = $scc->openedShoppingCart();
        return view('shopping_carts.excelImport')->with('shoppingCart', $shoppingCart);
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
//            ->addColumn('DetailNumber', function($data) { return $data->DetailNumber; })
//            ->addColumn('CustomerName', function($data) { return $data->CustomerName; })
//            ->addColumn('CustomerAddressName', function($data) { return $data->CustomerAddressName; })
//            ->addColumn('CustomerContactName', function($data) { return $data->CustomerContactName; })
//            ->addColumn('PaymentMethodName', function($data) { return $data->PaymentMethodName; })
//            ->addColumn('CurrencyName', function($data) { return $data->CurrencyName; })
//            ->addColumn('CustomerContractVoucherNumber', function($data) { return $data->CustomerContractVoucherNumber; })
//            ->addColumn('TransportModeName', function($data) { return $data->TransportModeName; })
//            ->addColumn('CustomerOrderVoucherNumber', function($data) { return $data->CustomerOrderVoucherNumber; })
            ->addColumn('action', function($row){
                  $btn = '';
                  if ($row->Opened == 0) {
                      $btn = '<a href="' . route('shoppingCarts.edit', [$row->Id]) . '"
                                 class="edit btn btn-success btn-sm editProduct" title="Módosítás"><i class="fa fa-paint-brush"></i></a>';
                      $btn = $btn.'<a href="' . route('shoppingCarts.destroy', [$row->Id]) . '"
                                 class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                      $btn = $btn.'<a href="' . route('shoppingCartDetailCreate', [$row->Id]) . '"
                                 class="btn btn-warning btn-sm deleteProduct" title="Tételek"><i class="far fa-list-alt"></i></a>';
                      $btn = $btn.'<a href="' . route('shoppingCartClose', [$row->Id]) . '"
                                 class="btn btn-primary btn-sm deleteProduct" title="Zárás"><i class="fas fa-store-slash"></i></a>';
                  }
                  if ($row->Opened == 1) {
                      $btn = '<a href="' . route('shoppingCartOpen', [$row->Id]) . '"
                                 class="btn btn-primary btn-sm deleteProduct" title="Nyítás"><i class="fas fa-store-alt"></i></a>';
                  }
                  return $btn;
              })
              ->rawColumns(['action'])
              ->make(true);
    }


    /**
     * Display a listing of the ShoppingCart.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

//                $data = ShoppingCart::all();
                $data = DB::table('ShoppingCart as t1')
                    ->join('ShoppingCartDetail as t2', 't2.ShoppingCart', '=', 't1.Id')
                    ->join('Currency as t3', 't3.Id', '=', 't1.Currency')
                    ->join('PaymentMethod as t4', 't4.Id', '=', 't1.PaymentMethod')
                    ->join('TransportMode as t5', 't5.Id', '=', 't1.TransportMode')
                    ->leftJoin('CustomerOrder as t6', 't6.Id', '=', 't1.CustomerOrder')
                    ->select(DB::raw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.DeliveryDate, t1.NetValue, t1.GrossValue, t1.VatValue, t1.Opened,
                                  t3.Name as CurrencyName, t4.Name as PaymentMethodName, t5.Name as TransportModeName, t6.VoucherNumber as CustomerOrderVoucherNumber,
                                  sum(1) as DetailNumber'))
                    ->whereNull('t1.deleted_at')
                    ->whereNull('t2.deleted_at')
                    ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.DeliveryDate', 't1.NetValue', 't1.GrossValue', 't1.VatValue', 't1.Opened',
                        'CurrencyName', 'PaymentMethodName', 'TransportModeName', 'CustomerOrderVoucherNumber')
                    ->get();
                return $this->dwData($data);

            }

            return view('shopping_carts.index');
        }
    }

    public function shoppingCartDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = ShoppingCartDetail::where('ShoppingCart', $id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('CurrencyName', function($data) { return $data->CurrencyName; })
                    ->addColumn('ProductName', function($data) { return $data->ProductName; })
                    ->addColumn('QuantityUnitName', function($data) { return $data->QuantityUnitName; })
                    ->addColumn('VatRate', function($data) { return $data->VatRate; })
                    ->addColumn('action', function($row){
                        $btn = '<a href="' . route('shoppingCartDetailDestroy', [$row->Id]) . '"
                                 class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            }

            return view('shopping_carts.index');
        }
    }

    public function sCDIndex($id)
    {
        return Response::json(ShoppingCartDetail::where('ShoppingCart', $id)->get());
    }

    /**
     * Show the form for creating a new ShoppingCart.
     *
     * @return Response
     */
    public function create(ShoppingCartOpened $scc)
    {
        if ( $scc->isOpenedShoppingCart() == 0 ) {
            return view('shopping_carts.create');
        } else {
            Flash::error(langClass::trans('Van már nyitott kosara!'))->important();
            return view('shopping_carts.index');
        }
    }

    /**
     * Store a newly created ShoppingCart in storage.
     *
     * @param CreateShoppingCartRequest $request
     *
     * @return Response
     */
    public function store(CreateShoppingCartRequest $request)
    {
        $input = $request->all();

        $shoppingCart = new ShoppingCart;

        $shoppingCart->VoucherNumber    = $input['VoucherNumber'];
        $shoppingCart->Customer         = myUser::user()->customerId;
        $shoppingCart->CustomerAddress  = $input['CustomerAddress'];
        $shoppingCart->CustomerContact  = myUser::user()->customercontact_id;
        $shoppingCart->VoucherNumber    = $input['VoucherNumber'];
        $shoppingCart->VoucherDate      = $input['VoucherDate'];
        $shoppingCart->DeliveryDate     = $input['DeliveryDate'];
        $shoppingCart->PaymentMethod    = $input['PaymentMethod'];
        $shoppingCart->Currency         = utilityClass::currencyId('HUF');
        $shoppingCart->CurrencyRate     = 1;
        $shoppingCart->TransportMode    = $input['TransportMode'];
        $shoppingCart->CustomerContract = NULL;
        $shoppingCart->DepositValue     = $input['DepositValue'];
        $shoppingCart->DepositPercent   = $input['DepositPercent'];
        $shoppingCart->NetValue         = $input['NetValue'];
        $shoppingCart->GrossValue       = $input['GrossValue'];
        $shoppingCart->VatValue         = $input['VatValue'];
        $shoppingCart->Comment          = $input['Comment'];
        $shoppingCart->Opened           = 0;
        $shoppingCart->CustomerOrder    = NULL;

        $shoppingCart->save();

        logClass::insertDeleteRecord( 1, "ShoppingCart", $shoppingCart->Id);

        return redirect(route('shoppingCarts.index'));
    }

    /**
     * Display the specified ShoppingCart.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $shoppingCart = $this->shoppingCartRepository->find($id);

        if (empty($shoppingCart)) {
            return redirect(route('shoppingCarts.index'));
        }

        return view('shopping_carts.show')->with('shoppingCart', $shoppingCart);
    }

    /**
     * Show the form for editing the specified ShoppingCart.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $shoppingCart = $this->shoppingCartRepository->find($id);

        if (empty($shoppingCart)) {
            return redirect(route('shoppingCarts.index'));
        }

        return view('shopping_carts.edit')->with('shoppingCart', $shoppingCart);
    }

    /**
     * Show the form for editing the specified ShoppingCart.
     *
     * @param int $id
     *
     * @return Response
     */
    public function editShoppingCart(ShoppingCartOpened $scc)
    {
        $shoppingCart = $scc->openedShoppingCart();

        if (empty($shoppingCart)) {
            return view('shopping_carts.create');
        }

        return view('shopping_carts.edit')->with('shoppingCart', $shoppingCart);
    }

    /**
     * Update the specified ShoppingCart in storage.
     *
     * @param int $id
     * @param UpdateShoppingCartRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateShoppingCartRequest $request)
    {
        $shoppingCart = $this->shoppingCartRepository->find($id);

        if (empty($shoppingCart)) {
            return redirect(route('shoppingCarts.index'));
        }

        $input = $request->all();

        DB::table('ShoppingCart')
            ->where('Id', $id)
            ->update([
                'VoucherNumber'    => $input['VoucherNumber'],
                'Customer'         => $input['Customer'],
                'CustomerAddress'  => $input['CustomerAddress'],
                'CustomerContact'  => $input['CustomerContact'],
                'VoucherNumber'    => $input['VoucherNumber'],
                'VoucherDate'      => $input['VoucherDate'],
                'DeliveryDate'     => $input['DeliveryDate'],
                'PaymentMethod'    => $input['PaymentMethod'],
                'Currency'         => $input['Currency'],
                'CurrencyRate'     => $input['CurrencyRate'],
                'TransportMode'    => $input['TransportMode'],
                'CustomerContract' => $input['CustomerContract'],
                'DepositValue'     => $input['DepositValue'],
                'DepositPercent'   => $input['DepositPercent'],
//                'NetValue'         => $input['NetValue'],
//                'GrossValue'       => $input['GrossValue'],
//                'VatValue'         => $input['VatValue'],
                'Comment'          => $input['Comment'],
                'Opened'           => $input['Opened'],
                'CustomerOrder'    => $input['CustomerOrder'],
                'updated_at'       => \Carbon\Carbon::now()
            ]);
        $modifiedShoppingCart = ShoppingCart::find($id);

        logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

        return redirect(route('shoppingCarts.edit', $id));

    }

    /**
     * Remove the specified ShoppingCart from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $shoppingCart = $this->shoppingCartRepository->find($id);

        if (empty($shoppingCart)) {
            return redirect(route('shoppingCarts.index'));
        }

        $this->shoppingCartRepository->delete($id);

        return redirect(route('shoppingCarts.index'));
    }

    public function cartDestroy($id)
    {

        $shoppingCart = $this->shoppingCartRepository->find($id);

        $details = ShoppingCartDetail::where('ShoppingCart', $id)->get();

        foreach ( $details as $detail ) {
            DB::table('ShoppingCartDetail')
                ->where('Id', $detail->Id)
                ->update([
                    'deleted_at'       => \Carbon\Carbon::now()
                ]);

            logClass::insertDeleteRecord( 5, "ShoppingCartDetail", $detail->Id);

        }

        DB::table('ShoppingCart')
            ->where('Id', $id)
            ->update([
                'deleted_at'       => \Carbon\Carbon::now()
            ]);

        logClass::insertDeleteRecord( 5, "ShoppingCart", $shoppingCart->Id);

        return redirect(route('shoppingCarts.index'));

    }

    public function close($id)
    {

        $shoppingCart = ShoppingCart::find($id);

        DB::table('ShoppingCart')
            ->where('Id', $id)
            ->update([
                'Opened'     => 1,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        $modifiedShoppingCart = ShoppingCart::find($id);

        logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

        return redirect(route('shoppingCarts.index'));
    }

    public function open($id)
    {

        $shoppingCart = ShoppingCart::OrderBy('Id', 'desc')->first();

        if ( $id != $shoppingCart->Id ) {
            Flash::error(langClass::trans('A tétel nem nyitható vissza!'))->important();
        } else {
            DB::table('ShoppingCart')
                ->where('Id', $id)
                ->update([
                    'Opened'     => 0,
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            $modifiedShoppingCart = ShoppingCart::find($id);

            logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

        }

        return redirect(route('shoppingCarts.index'));
    }

    public function importExcel(Request $request)
    {
        if (empty($request->import_file)) {
            Flash::error(langClass::trans('Nem választott filet!'))->important();
            return back();
        }
        if ( $request->code != 0 && $request->quantity != 0 && $request->code == $request->quantity) {
            Flash::error(langClass::trans('A két mező nem egyezhet meg!'))->important();
            return back();
        }
        $array = Excel::toArray(new excelImportImport,  $request->import_file);
        if ( $request->code > count($array[0][0]) || $request->quantity > count($array[0][0]) ) {
            Flash::error(langClass::trans('A mező nem lehet nagyobb mint az oszlopok száma!'))->important();
            return back();
        }

        session(['excelCode' => $request->code]);
        session(['excelQuantity' => $request->quantity]);
        DB::table('excelimport')->where('user_id', myUser::user()->id)->delete();

        Excel::import(new excelImportImport, $request->import_file);
        shoppingCartClass::excelImportToShoppingCartDetail();

        return back();
    }

    public function excelImportUseRecordsDelete()
    {
        DB::table('excelimport')->where('user_id', myUser::user()->id)->delete();
        return back();
    }

}


