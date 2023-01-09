<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerContactFavoriteProductRequest;
use App\Http\Requests\UpdateCustomerContactFavoriteProductRequest;
use App\Repositories\CustomerContactFavoriteProductRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use Auth;
use DB;
use DataTables;
use myUser;

use Illuminate\Support\Facades\Cache;

use App\Models\CustomerContactFavoriteProduct;

class CustomerContactFavoriteProductController extends AppBaseController
{
    /** @var  CustomerContactFavoriteProductRepository */
    private $customerContactFavoriteProductRepository;

    public function __construct(CustomerContactFavoriteProductRepository $customerContactFavoriteProductRepo)
    {
        $this->customerContactFavoriteProductRepository = $customerContactFavoriteProductRepo;
    }

    public function dwData($data)
    {
        return Datatables::of($data)
              ->addIndexColumn()
              ->addColumn('ProductName', function($data) { return $data->product->Name; })
              ->addColumn('action', function($row){
                  $btn = '<a href="' . route('cCFPDestroyMe', [$row->id]) . '"
                             class="btn btn-danger btn-sm deleteProduct" title="Törlés"><i class="far fa-heart"></i></a>';
                  return $btn;
              })
              ->rawColumns(['action'])
              ->make(true);
    }


    /**
     * Display a listing of the CustomerContactFavoriteProduct.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if( \myUser::check() ){

            if ($request->ajax()) {

//                $data = CustomerContactFavoriteProduct::all();

                $data = Cache::remember('allCustomerContactFavoriteProduct', 3600, function() {
                    return CustomerContactFavoriteProduct::all();
                });
                return $this->dwData($data);

            }

            return view('customer_contact_favorite_products.index');
        }
    }

    /**
     * Show the form for creating a new CustomerContactFavoriteProduct.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_contact_favorite_products.create');
    }

    /**
     * Store a newly created CustomerContactFavoriteProduct in storage.
     *
     * @param CreateCustomerContactFavoriteProductRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerContactFavoriteProductRequest $request)
    {
        $input = $request->all();

        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->create($input);

        Flash::success('Customer Contact Favorite Product saved successfully.');

        return redirect(route('customerContactFavoriteProducts.index'));
    }

    /**
     * Display the specified CustomerContactFavoriteProduct.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->find($id);

        if (empty($customerContactFavoriteProduct)) {
            Flash::error('Customer Contact Favorite Product not found');

            return redirect(route('customerContactFavoriteProducts.index'));
        }

        return view('customer_contact_favorite_products.show')->with('customerContactFavoriteProduct', $customerContactFavoriteProduct);
    }

    /**
     * Show the form for editing the specified CustomerContactFavoriteProduct.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->find($id);

        if (empty($customerContactFavoriteProduct)) {
            Flash::error('Customer Contact Favorite Product not found');

            return redirect(route('customerContactFavoriteProducts.index'));
        }

        return view('customer_contact_favorite_products.edit')->with('customerContactFavoriteProduct', $customerContactFavoriteProduct);
    }

    /**
     * Update the specified CustomerContactFavoriteProduct in storage.
     *
     * @param int $id
     * @param UpdateCustomerContactFavoriteProductRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerContactFavoriteProductRequest $request)
    {
        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->find($id);

        if (empty($customerContactFavoriteProduct)) {
            Flash::error('Customer Contact Favorite Product not found');

            return redirect(route('customerContactFavoriteProducts.index'));
        }

        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->update($request->all(), $id);

        Flash::success('Customer Contact Favorite Product updated successfully.');

        return redirect(route('customerContactFavoriteProducts.index'));
    }

    /**
     * Remove the specified CustomerContactFavoriteProduct from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->find($id);

        if (empty($customerContactFavoriteProduct)) {
            Flash::error('Customer Contact Favorite Product not found');

            return redirect(route('customerContactFavoriteProducts.index'));
        }

        $this->customerContactFavoriteProductRepository->delete($id);

        Flash::success('Customer Contact Favorite Product deleted successfully.');

        return redirect(route('customerContactFavoriteProducts.index'));
    }

    public function productCategoryProductindex(Request $request, $category)
    {
        if( \myUser::check() ){

            if ($request->ajax()) {

                $data = DB::table('product')
                        ->where(function($query) use ($category) {
                            if (is_null($category) || $category == -999999) {
                                $query->whereNotNull('ProductCategory');
                            } else {
                                $query->where('ProductCategory', '=', $category);
                            }
                        })
                        ->whereNotIn('Id', function ($query) {
                            return $query->from('customercontactfavoriteproduct')->select('product_id')->where('customercontact_id', myUser::user()->customercontact_id)->get();
                        })
                        ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->make(true);

            }

            return back();
        }
    }

    public function destroyMe($id)
    {
        $customerContactFavoriteProduct = $this->customerContactFavoriteProductRepository->find($id);

        if (empty($customerContactFavoriteProduct)) {
            Flash::error('Nincs ilyen kedvenc termék')->important();

            return redirect(route('customerContactFavoriteProducts.index'));
        }

        $this->customerContactFavoriteProductRepository->delete($id);
       return redirect(route('customerContactFavoriteProducts.index'));
    }

}
