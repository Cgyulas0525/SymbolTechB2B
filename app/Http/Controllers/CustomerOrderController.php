<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerOrderRequest;
use App\Http\Requests\UpdateCustomerOrderRequest;
use App\Repositories\CustomerOrderRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderDetail;

class CustomerOrderController extends AppBaseController
{
    /** @var  CustomerOrderRepository */
    private $customerOrderRepository;

    public function __construct(CustomerOrderRepository $customerOrderRepo)
    {
        $this->customerOrderRepository = $customerOrderRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('tetelszam', function($data) { return $data->tetelszam; })
            ->addColumn('currencyName', function($data) { return $data->currencyName; })
            ->addColumn('statusName', function($data) { return $data->statusName; })
            ->addColumn('action', function($row){
                  $btn = '';
                  if ($row->tetelszam > 0 && $row->NetValue > 0) {
                      $btn = '<a href="' . route('customerOrders.edit', [$row->Id]) . '"
                                 class="edit btn btn-success btn-sm editProduct" title="Tételek"><i class="far fa-list-alt"></i></a>';
                  }
                  return $btn;
              })
              ->rawColumns(['action'])
              ->make(true);
    }

    public function allData()
    {
        return DB::table('customerorder as t1')
            ->selectRaw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.NetValue, t1.VatValue, t1.GrossValue , t3.Name as currencyName, SUM(1) as tetelszam, t4.Name as statusName', )
            ->join('customerorderdetail as t2', 't2.CustomerOrder', '=', 't1.Id')
            ->join('currency as t3', 't3.Id', '=', 't1.Currency')
            ->leftJoin('customerorderstatus as t4', 't4.Id', '=', 't1.CustomerOrderStatus')
            ->where('t1.Customer', session('customer_id'))
            ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.NetValue', 't1.VatValue', 't1.GrossValue', 't3.Name', 't4.Name')
            ->get();
    }


    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $data = $this->allData();
                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }

    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAllThisYear(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $kezdo = date('Y-m-d', strtotime('first day of january last year'));
                $veg   = date('Y-m-d', strtotime('last day of december this year'));
                $data = $this->allData()->whereBetween('VoucherDate', [$kezdo, $veg]);
                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }

    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexOwn(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $data = $this->allData()->where('t1.CustomerContact', myUser::user()->customercontact_id);
                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }

    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexYearAllOwn(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $kezdo = date('Y-m-d', strtotime('first day of january last year'));
                $veg   = date('Y-m-d', strtotime('last day of december this year'));
                $data = $this->allData()->where('t1.Customer', session('customer_id'))->where('t1.CustomerContact', myUser::user()->customercontact_id)->whereBetween('VoucherDate', [$kezdo, $veg]);
                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }

    public function allShoppingCart()
    {
        return DB::table('shoppingcart as t1')
            ->selectRaw('t1.Id, t1.VoucherNumber, t1.VoucherDate, t1.NetValue, t1.VatValue, t1.GrossValue , t3.Name as currencyName, SUM(1) as tetelszam')
            ->join('shoppingcartdetail as t2', 't2.ShoppingCart', '=', 't1.Id')
            ->join('currency as t3', 't3.Id', '=', 't1.Currency')
            ->where('t1.Customer', session('customer_id'))
            ->where('t1.CustomerContact', myUser::user()->customercontact_id)
            ->where('t1.Opened', 1)
            ->groupBy('t1.Id', 't1.VoucherNumber', 't1.VoucherDate', 't1.NetValue', 't1.VatValue', 't1.GrossValue', 't3.Name')
            ->get();
    }

    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexSC(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $data = $this->allShoppingCart();
                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }

    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexSCThisYear(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $kezdo = date('Y-m-d', strtotime('first day of january last year'));
                $veg   = date('Y-m-d', strtotime('last day of december this year'));
                $data = $this->allShoppingCart()->whereBetween('VoucherDate', [$kezdo, $veg]);
                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }

    /**
     * Display a listing of the CustomerOrder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexCOLastTreeMonth(Request $request)
    {
        if( myUser::check() ){
            if ($request->ajax()) {
                $kezdo = date('Y-m-d', strtotime('today - 3 months'));
                $veg   = date('Y-m-d', strtotime('today'));
                $data = $this->allData()->whereBetween('VoucherDate', [$kezdo, $veg]);

                return $this->dwData($data->where('tetelszam', '>', 0)->where('NetValue', '>', 0));
            }
            return view('customer_orders.index');
        }
    }



    public function customerOrderDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = CustomerOrderDetail::where('CustomerOrder', $id)->get();
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('CurrencyName', function($data) { return $data->CurrencyName; })
                    ->addColumn('ProductName', function($data) { return $data->ProductName; })
                    ->addColumn('QuantityUnitName', function($data) { return $data->QuantityUnitName; })
                    ->addColumn('VatRate', function($data) { return $data->VatRate; })
                    ->addColumn('StatusName', function($data) { return $data->StatusName; })
                    ->make(true);

            }

            return view('customer_orders.index');
        }
    }


    /**
     * Show the form for creating a new CustomerOrder.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_orders.create');
    }

    /**
     * Store a newly created CustomerOrder in storage.
     *
     * @param CreateCustomerOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerOrderRequest $request)
    {
        $input = $request->all();

        $customerOrder = $this->customerOrderRepository->create($input);

        Flash::success('Customer Order saved successfully.');

        return redirect(route('customerOrders.index'));
    }

    /**
     * Display the specified CustomerOrder.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerOrder = $this->customerOrderRepository->find($id);

        if (empty($customerOrder)) {
            Flash::error('Customer Order not found');

            return redirect(route('customerOrders.index'));
        }

        return view('customer_orders.show')->with('customerOrder', $customerOrder);
    }

    /**
     * Show the form for editing the specified CustomerOrder.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerOrder = $this->customerOrderRepository->find($id);

        if (empty($customerOrder)) {
            Flash::error('Customer Order not found');

            return redirect(route('customerOrders.index'));
        }

        return view('customer_orders.edit')->with('customerOrder', $customerOrder);
    }

    /**
     * Update the specified CustomerOrder in storage.
     *
     * @param int $id
     * @param UpdateCustomerOrderRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerOrderRequest $request)
    {
        $customerOrder = $this->customerOrderRepository->find($id);

        if (empty($customerOrder)) {
            Flash::error('Customer Order not found');

            return redirect(route('customerOrders.index'));
        }

        $customerOrder = $this->customerOrderRepository->update($request->all(), $id);

        Flash::success('Customer Order updated successfully.');

        return redirect(route('customerOrders.index'));
    }

    /**
     * Remove the specified CustomerOrder from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerOrder = $this->customerOrderRepository->find($id);

        if (empty($customerOrder)) {
            Flash::error('Customer Order not found');

            return redirect(route('customerOrders.index'));
        }

        $this->customerOrderRepository->delete($id);

        Flash::success('Customer Order deleted successfully.');

        return redirect(route('customerOrders.index'));
    }
}
