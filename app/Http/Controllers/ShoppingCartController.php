<?php

namespace App\Http\Controllers;

use App\Classes\langClass;
use App\Http\Requests\CreateShoppingCartRequest;
use App\Http\Requests\UpdateShoppingCartRequest;
use App\Imports\excelImportImport;
use App\Repositories\ShoppingCartRepository;

use Doctrine\DBAL\Driver\PDO\Exception;
use Illuminate\Http\Request;
use Flash;
use Maatwebsite\Excel\Facades\Excel;
use Response;

use DB;
use myUser;
use logClass;
use shoppingCartClass;

use App\Models\ShoppingCart;

use App\Classes\ShoppingCart\ShoppingCartOpened;

use App\Traits\ShoppingCart\SCDetailIndexTrait;
use App\Traits\ShoppingCart\ShoppingCartIndexTrait;
use App\Traits\ShoppingCart\SCDIndexTrait;
use App\Traits\Excel\ExcelImportTrait;
use App\Traits\Excel\ExcelIndexTrait;
use App\Traits\Excel\ExcelBetoltTrait;

use App\Services\ShoppingCartService;


class ShoppingCartController extends AppBaseController
{
    /** @var  ShoppingCartRepository */
    private $shoppingCartRepository;
    private $shoppingCartService;

    public function __construct(ShoppingCartRepository $shoppingCartRepo)
    {
        $this->shoppingCartRepository = $shoppingCartRepo;
        $this->shoppingCartService = new ShoppingCartService();
    }

    use SCDetailIndexTrait, ExcelImportTrait, ExcelIndexTrait, ExcelBetoltTrait, ShoppingCartIndexTrait, SCDIndexTrait;

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
        $this->shoppingCartService->shoppingCartInsert($input);

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
        $shoppingCart = ShoppingCart::OwnOpen(myUser::user()->customerId, myUser::user()->customercontact_id)->first();

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

        DB::beginTransaction();

        try {

            $input = $request->all();
            $modifiedShoppingCart = $this->shoppingCartService->shoppingCartUpdate($input, $id);

            logClass::modifyRecord( "ShoppingCart", $shoppingCart, $modifiedShoppingCart);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        return redirect(route('editShoppingCart'));

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

        $this->shoppingCartService->shoppingCartDelete($shoppingCart);

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
            $this->shoppingCartService->shoppingCartOpenedUpdate($shoppingCart);
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


