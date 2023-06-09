<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerOrderRepository;
use App\Http\Controllers\AppBaseController;

use Response;

use App\Models\ShoppingCart;

use App\Traits\CustomerOrder\CustomerOrderIndexTrait;
use App\Traits\CustomerOrder\CustomerOrderDetailIndexTrait;

//use App\Traits\CustomerOrder\CustomerOrderIndexAllThisYearTrait;
//use App\Traits\CustomerOrder\CustomerOrderIndexOwnTrait;
//use App\Traits\CustomerOrder\CustomerOrderIndexYearAllOwnTrait;
//use App\Traits\CustomerOrder\CustomerOrderIndexSCTrait;
//use App\Traits\CustomerOrder\CustomerOrderIndexSCThisYearTrait;
use App\Traits\CustomerOrder\CustomerOrderIndexCOLastThreeMonthTrait;
//use App\Traits\CustomerOrder\ShoppingCartDetailIndexTrait;

class CustomerOrderController extends AppBaseController
{
    /** @var  CustomerOrderRepository */
    private $customerOrderRepository;

    public function __construct(CustomerOrderRepository $customerOrderRepo)
    {
        $this->customerOrderRepository = $customerOrderRepo;
    }

    use CustomerOrderIndexTrait, CustomerOrderDetailIndexTrait, CustomerOrderIndexCOLastThreeMonthTrait;

    //    use CustomerOrderIndexTrait, CustomerOrderIndexAllThisYearTrait, CustomerOrderIndexOwnTrait,
//        CustomerOrderIndexYearAllOwnTrait, CustomerOrderIndexSCTrait, CustomerOrderIndexSCThisYearTrait,
//        CustomerOrderIndexCOLastThreeMonthTrait, CustomerOrderDetailIndexTrait, ShoppingCartDetailIndexTrait;

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
            return redirect(route('customerOrderIndex', ['customerContact' => session('coContact'), 'year' => session('coYear')]));
        }

        return view('customer_orders.edit')->with('customerOrder', $customerOrder);
    }

    /**
     * Show the form for editing the specified CustomerOrder.
     *
     * @param int $id
     *
     * @return Response
     */
    public function editSc($id)
    {
        $shoppingCart = ShoppingCart::find($id);

        if (empty($shoppingCart)) {
            return redirect(route('customerOrderIndex'));
        }

        return view('customer_orders.editSC')->with('shoppingCart', $shoppingCart);
    }

}
