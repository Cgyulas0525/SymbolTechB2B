<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Flash;
use Response;
use myUser;
use App\Models\CustomerOffer;
use App\Models\CustomerOfferDetail;
use DB;
use DataTables;
use utilityClass;

class CustomerOfferController extends Controller
{
    /**
     * Display a listing of the Megrendelesfej.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function oneOffer($id)
    {
        if (myUser::check()) {

            $customerOffer = CustomerOffer::find($id);

            return view('customerOffer.oneOffer')->with('customerOffer', $customerOffer);

        }
        return view('/');
    }

    public function dwData($data)
    {
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('productName', function($data) { return $data->productName; })
            ->addColumn('currencyName', function($data) { return $data->currencyName; })
            ->addColumn('quantityUnitName', function($data) { return $data->quantityUnitName; })
            ->addColumn('kep', function($row){
                $image = '<img class="brand-image elevation-3 picture-small" src="data:image/png;base64,' .
                           utilityClass::echoPicture( !empty($row->productPicture) ? $row->productPicture : session('noAviablePicture')) .'">';
                return $image;
            })
            ->rawColumns(['kep'])
            ->make(true);
    }

    /**
     * Display a listing of the Users.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function customerOfferDetailIndex(Request $request, $id)
    {
        if( myUser::check() ){

            if ($request->ajax()) {

                $data = CustomerOfferDetail::where('CustomerOffer', $id)->get();
                return $this->dwData($data);

            }

            return view('users.index');
        }
    }


}
