<?php

namespace App\Http\Controllers;

use App\Classes\langClass;
use App\Http\Requests\CreateShoppingCartDetailRequest;
use App\Http\Requests\UpdateShoppingCartDetailRequest;
use App\Models\ShoppingCart;
use App\Repositories\ShoppingCartDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;
use logClass;

use App\Models\ShoppingCartDetail;
use Carbon\Carbon;

class ShoppingCartDetailController extends AppBaseController
{
    /** @var  ShoppingCartDetailRepository */
    private $shoppingCartDetailRepository;

    public function __construct(ShoppingCartDetailRepository $shoppingCartDetailRepo)
    {
        $this->shoppingCartDetailRepository = $shoppingCartDetailRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
              ->addIndexColumn()
              ->addColumn('action', function($row){
                  $btn = '<a href="#"
                             class="edit btn btn-success btn-sm editProduct" title="Módosítás"><i class="fa fa-paint-brush"></i></a>';
                  $btn = $btn.'<a href="#"
                             class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="fa fa-trash"></i></a>';
                  return $btn;
              })
              ->rawColumns(['action'])
              ->make(true);
    }


    /**
     * Display a listing of the ShoppingCartDetail.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = ShoppingCartDetail::where('ShoppingCart', $id)->get();
                return $this->dwData($data);

            }

            return view('shopping_cart_details.index');
        }
    }

    /**
     * Show the form for creating a new ShoppingCartDetail.
     *
     * @return Response
     */
    public function create($id)
    {
        $shoppingCart = ShoppingCart::find($id);

        return view('shopping_cart_details.create')->with('shoppingCart', $shoppingCart);
    }

    public function dbRaw($Id) {
        return DB::raw('getLastProductPrice('.  myUser::user()->customerId .','.$Id.', -1, -1) as lastPrice,
                                     getProductPrice('. myUser::user()->customerId .','.$Id.', 1, -1, -1) as productPrice,
                                     discountPercentage('. myUser::user()->customerId .','.$Id.', 1, -1, -1) as discountPercent' );
    }

    public function productIndex(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

//                $vmi = DB::raw('MAX(t2.ValidFrom) as rc, getLastProductPrice('.  myUser::user()->customerId .', t1.Id, -1, -1) as lastPrice,
//                                     getProductPrice('. myUser::user()->customerId .', t1.Id, 1, -1, -1) as productPrice,
//                                     discountPercentage('. myUser::user()->customerId .', t1.Id, 1, -1, -1) as discountPercent' );


                $data = DB::table('Product as t1')
                            ->join('ProductPrice as t2', 't2.Product', '=', 't1.Id')
                            ->leftJoin('ProductCategory as t3', 't3.Id', '=', 't1.ProductCategory' )
                            ->leftJoin('QuantityUnit as t4', 't4.Id', '=', 't1.QuantityUnit')
                            ->select('t1.Id', 't1.Code', 't1.Barcode', 't1.Name as ProductName', 't3.Name as ProductCategoryName',
                                't4.Name as QuantityUnitName', $this->dbRaw('t1.Id'))
                            ->where('t1.Inactive', 0)
                            ->where('t1.Service', 0)
                            ->where('t1.Deleted', 0)
                            ->where('t1.ProductCategory', $request->ProductCategory)
                            ->where('t2.PriceCategory', function($query) {
                                    $query->from('Customer as t5')->select('t5.PriceCategory')->where('Id', myUser::user()->customerId)->first();
                                })
//                            ->where('t2.PriceCategory', 2)
                            ->where('t2.Currency', -1)
                            ->where(DB::raw('getProductPrice('.myUser::user()->customerId. ', t1.Id, 1, -1, -1)'), '>', 0)
                            ->groupBy('t1.Id', 't1.Code', 't1.Barcode', 't1.Name', 't3.Name', 't4.Name')
                            ->get();


                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Quantity', function($data) { return 0; })
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }

    }

    public function favoriteProductIndex(Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = DB::table('Product as t1')
                    ->join('ProductPrice as t2', 't2.Product', '=', 't1.Id')
                    ->join('customercontactfavoriteproduct as t5', function($join) {
                        $join->on('t1.Id', '=', 't5.product_id')
                        ->where('t5.customercontact_id', myUser::user()->customercontact_id);
                    })
                    ->leftJoin('ProductCategory as t3', 't3.Id', '=', 't1.ProductCategory' )
                    ->leftJoin('QuantityUnit as t4', 't4.Id', '=', 't1.QuantityUnit')
                    ->select('t1.Id', 't1.Code', 't1.Barcode', 't1.Name as ProductName', 't3.Name as ProductCategoryName',
                        't4.Name as QuantityUnitName', $this->dbRaw('t1.Id'))
                    ->where('t1.Inactive', 0)
                    ->where('t1.Service', 0)
                    ->where('t1.Deleted', 0)
                    ->where('t2.PriceCategory', 2)
                    ->where('t2.Currency', -1)
                    ->groupBy('t1.Id', 't1.Code', 't1.Barcode', 't1.Name', 't3.Name', 't4.Name')
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Quantity', function($data) { return 0; })
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }

    }

    public function customerOfferProductIndex (Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $item1 = DB::table('CustomerOffer as t1')
                    ->join('CustomerOfferCustomer as t2', 't2.CustomerOffer', '=', 't1.Id')
                    ->join('CustomerOfferDetail as t3', 't3.CustomerOffer', '=', 't1.Id')
                    ->join('Product as t4', 't4.Id', '=', 't3.Product')
                    ->leftJoin('ProductCategory as t5', 't5.Id', '=', 't4.ProductCategory' )
                    ->leftJoin('QuantityUnit as t6', 't6.Id', '=', 't4.QuantityUnit')
                    ->select('t4.Id', 't4.Code', 't4.Barcode', 't4.Name as ProductName', 't5.Name as ProductCategoryName',
                        't6.Name as QuantityUnitName', $this->dbRaw('t4.Id'))
                    ->where('t2.Customer', myUser::user()->customer_id)
                    ->where( 't1.ValidFrom', '<=', Carbon::parse(now()))
                    ->where( 't1.ValidTo', '>=', Carbon::parse(now()))
                    ->groupBy('t4.Id');

                $item2 = DB::table('CustomerOffer as t1')
                    ->join('CustomerOfferCustomer as t2', 't2.CustomerOffer', '=', 't1.Id')
                    ->join('CustomerOfferDetail as t3', 't3.CustomerOffer', '=', 't1.Id')
                    ->join('Product as t4', 't4.Id', '=', 't3.Product')
                    ->leftJoin('ProductCategory as t5', 't5.Id', '=', 't4.ProductCategory' )
                    ->leftJoin('QuantityUnit as t6', 't6.Id', '=', 't4.QuantityUnit')
                    ->join('CustomerCategory as t7', 't7.Id', '=', 't2.CustomerCategory')
                    ->join('Customer as t8', 't8.CustomerCategory', '=', 't7.Id')
                    ->select('t4.Id', 't4.Code', 't4.Barcode', 't4.Name as ProductName', 't5.Name as ProductCategoryName',
                        't6.Name as QuantityUnitName', $this->dbRaw('t4.Id'))
                    ->whereNotNull('t2.CustomerCategory')
                    ->where('t8.Id', myUser::user()->customer_id)
                    ->where( 't1.ValidFrom', '<=', Carbon::parse(now()))
                    ->where( 't1.ValidTo', '>=', Carbon::parse(now()))
                    ->groupBy('t4.Id')
                    ->union($item1);


                $data = DB::query()->fromSub($item2, 'item')->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Quantity', function($data) { return 0; })
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }

    }

    public function customerContractProductIndex (Request $request)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $item1 = DB::table('CustomerContract as t1')
                    ->join( 'CustomerContractDetail as t2', 't2.CustomerContract', '=', 't1.Id')
                    ->join('Product as t3', 't3.Id', '=', 't2.Product')
                    ->leftJoin('ProductCategory as t4', 't4.Id', '=', 't3.ProductCategory' )
                    ->leftJoin('QuantityUnit as t5', 't5.Id', '=', 't3.QuantityUnit')
                    ->select('t3.Id', 't3.Code', 't3.Barcode', 't3.Name as ProductName', 't4.Name as ProductCategoryName',
                        't5.Name as QuantityUnitName', $this->dbRaw('t3.Id'))
                    ->where('t1.ValidFrom', '<=' , Carbon::parse(now()))
                    ->where('t1.Customer', myUser::user()->customer_id)
                    ->whereNull('t1.ValidTo');

                $item2 = DB::table('CustomerContract as t1')
                    ->join( 'CustomerContractDetail as t2', 't2.CustomerContract', '=', 't1.Id')
                    ->join('Product as t3', 't3.Id', '=', 't2.Product')
                    ->leftJoin('ProductCategory as t4', 't4.Id', '=', 't3.ProductCategory' )
                    ->leftJoin('QuantityUnit as t5', 't5.Id', '=', 't3.QuantityUnit')
                    ->select('t3.Id', 't3.Code', 't3.Barcode', 't3.Name as ProductName', 't4.Name as ProductCategoryName',
                        't5.Name as QuantityUnitName', $this->dbRaw('t3.Id'))
                    ->where('t1.ValidFrom', '<=' , Carbon::parse(now()))
                    ->where('t1.Customer', myUser::user()->customer_id)
                    ->where('t1.ValidTo', '>=', Carbon::parse(now()))
                    ->union($item1);

                $data = DB::query()->fromSub($item2, 'item')->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Quantity', function($data) { return 0; })
                    ->make(true);

            }

            return view('shopping_cart_details.index');
        }

    }


    /**
     * Store a newly created ShoppingCartDetail in storage.
     *
     * @param CreateShoppingCartDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateShoppingCartDetailRequest $request)
    {
        $input = $request->all();

        $shoppingCartDetail = $this->shoppingCartDetailRepository->create($input);

        Flash::success(langClass::trans('Kosár tétel mentés sikeres.'));

        return redirect(route('shoppingCartDetails.index'));
    }

    /**
     * Display the specified ShoppingCartDetail.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $shoppingCartDetail = $this->shoppingCartDetailRepository->find($id);

        if (empty($shoppingCartDetail)) {
            Flash::error(langClass::trans('Kosár tétel nem található'));

            return redirect(route('shoppingCartDetails.index'));
        }

        return view('shopping_cart_details.show')->with('shoppingCartDetail', $shoppingCartDetail);
    }

    /**
     * Show the form for editing the specified ShoppingCartDetail.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $shoppingCartDetail = $this->shoppingCartDetailRepository->find($id);

        if (empty($shoppingCartDetail)) {
            Flash::error(langClass::trans('Kosár tétel nem található'));

            return redirect(route('shoppingCartDetails.index'));
        }

        return view('shopping_cart_details.edit')->with('shoppingCartDetail', $shoppingCartDetail);
    }

    /**
     * Update the specified ShoppingCartDetail in storage.
     *
     * @param int $id
     * @param UpdateShoppingCartDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateShoppingCartDetailRequest $request)
    {
        $shoppingCartDetail = $this->shoppingCartDetailRepository->find($id);

        if (empty($shoppingCartDetail)) {
            Flash::error(langClass::trans('Kosár tétel nem található'));

            return redirect(route('shoppingCartDetails.index'));
        }

        $shoppingCartDetail = $this->shoppingCartDetailRepository->update($request->all(), $id);

        Flash::success(langClass::trans('A kosár tétel modosítás sikeres.'));

        return redirect(route('shoppingCartDetails.index'));
    }

    /**
     * Remove the specified ShoppingCartDetail from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $shoppingCartDetail = $this->shoppingCartDetailRepository->find($id);
        $shoppingCart       = ShoppingCart::where('Id', $shoppingCartDetail->ShoppingCart)->first();

        if (empty($shoppingCartDetail)) {
            return redirect(route('shoppingCartDetails.index'));
        }

        DB::table('ShoppingCart')
            ->where('Id', $shoppingCartDetail->ShoppingCart)
            ->update([
                'NetValue'   => $shoppingCart->NetValue - $shoppingCartDetail->NetValue,
                'VatValue'   => $shoppingCart->VatValue - $shoppingCartDetail->VatValue,
                'GrossValue' => $shoppingCart->GrossValue - $shoppingCartDetail->GrossValue,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        $modifiedShoppingCart = ShoppingCart::where('Id', $shoppingCartDetail->ShoppingCart)->first();
        logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);


        DB::table('ShoppingCartDetail')
            ->where('Id', $id)
            ->delete();

        logClass::insertDeleteRecord( 5, "ShoppingCartDetail", $id);


        $shoppingCart       = ShoppingCart::where('Id', $shoppingCartDetail->ShoppingCart)->first();

        return view('shopping_carts.edit')->with('shoppingCart', $shoppingCart);

    }
}
