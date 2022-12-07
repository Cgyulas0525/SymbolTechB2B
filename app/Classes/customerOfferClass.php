<?php
namespace App\Classes;

use DB;

Class customerOfferClass{

    /*
     * Adott partner akciós ajánlatai
     *
     * @param $id integer
     *
     * @return CustomerOffers
    */
    public static function customerOffers($id)
    {
        $coffers1 = DB::table('customeroffer as t1')
            ->select('t1.*')
            ->where('t1.ValidFrom', '<=' , \Carbon\Carbon::now())
            ->where('t1.ValidTo', '>=', \Carbon\Carbon::now())
            ->where('t1.CustomerDepend', 0);

        $coffers2 = DB::table('customeroffer as t1')
            ->join('customeroffercustomer as t2', 't2.CustomerOffer', '=', 't1.Id')
            ->select('t1.*')
            ->where('t2.Customer', $id)
            ->where('t1.ValidFrom', '<=' , \Carbon\Carbon::now())
            ->where('t1.ValidTo', '>=', \Carbon\Carbon::now())
            ->where('t1.CustomerDepend', 1)
            ->where('t2.Forbid', 0);


        $coffers3 = DB::table('customeroffer as t1')
            ->join('customeroffercustomer as t2', 't2.CustomerOffer', '=', 't1.Id')
            ->join('customercategory as t3', 't3.Id', '=', 't2.CustomerCategory')
            ->join('customer as t4', 't4.CustomerCategory', '=', 't3.Id')
            ->select('t1.*')
            ->where('t1.ValidFrom', '<=' , \Carbon\Carbon::now())
            ->where('t1.ValidTo', '>=', \Carbon\Carbon::now())
            ->where('t1.CustomerDepend', 1)
            ->whereNotNull('t2.CustomerCategory')
            ->where('t4.Id', $id)
            ->where('t2.Forbid', 0)
            ->union($coffers1)
            ->union($coffers2);

        return DB::query()->fromSub($coffers3, 'coffers')->get();
    }

    /*
     * Akcióban résztvevő termékek darabszáma
     *
     * @param $id integer
     *
     * @return integer
     */
    public static function customerOfferProductsCount($id)
    {
        return DB::table('customerofferdetail')
            ->where('CustomerOffer', $id)
            ->distinct()
            ->count('Product');
    }
}
