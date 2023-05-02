<?php

namespace App\Http\Controllers;

use App\Classes\langClass;
use App\Http\Requests\CreateShoppingCartDetailRequest;
use App\Http\Requests\UpdateShoppingCartDetailRequest;
use App\Models\ShoppingCart;
use App\Repositories\ShoppingCartDetailRepository;
use Flash;
use Response;

use App\Services\ShoppingCartService;
use App\Services\ShoppingCartDetailService;

use App\Traits\ShoppingCart\ProductIndexTrait;
use App\Traits\ShoppingCart\FavoriteProductIndexTrait;
use App\Traits\ShoppingCart\CustomerOfferProductIndexTrait;
use App\Traits\ShoppingCart\CustomerContractProductIndexTrait;
use App\Traits\ShoppingCart\ShoppingCartDetailIndexTrait;
use App\Traits\ShoppingCart\BeforeSCDDestroyTrait;
use App\Traits\ShoppingCart\ShoppingCartDetailDestroyTrait;

class ShoppingCartDetailController extends AppBaseController
{
    /** @var  ShoppingCartDetailRepository */
    private $shoppingCartDetailRepository;
    private $shoppingCartService;
    private $shoppingCartDetailService;

    public function __construct(ShoppingCartDetailRepository $shoppingCartDetailRepo)
    {
        $this->shoppingCartDetailRepository = $shoppingCartDetailRepo;
        $this->shoppingCartService = new ShoppingCartService();
        $this->shoppingCartDetailService = new ShoppingCartDetailService();
    }

    use ProductIndexTrait, FavoriteProductIndexTrait, CustomerOfferProductIndexTrait, CustomerContractProductIndexTrait,
        ShoppingCartDetailIndexTrait, BeforeSCDDestroyTrait, ShoppingCartDetailDestroyTrait;

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

}
