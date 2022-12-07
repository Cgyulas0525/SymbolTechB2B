<?php
//$whs = DB::table('warehousedailybalance as t1')
//    ->selectRaw('t1.Product, t1.Warehouse, max(t1.Date) as date')
//    ->groupByRaw('t1.Product, t1.Warehouse');
//
//$whs1 = DB::table('warehousedailybalance as t1')
//    ->joinSub($whs, 'whs', function ($join) {
//        $join->on('t1.Product', 'whs.Product' )->on('t1.Warehouse', 'whs.Warehouse')->on('t1.Date', 'whs.date');
//    })
//    ->selectRaw('t1.Product, sum(Balance) as balance')
//    ->groupByRaw('t1.Product');
//
//$whs2 = DB::table('product as t1')
//    ->leftJoinSub($whs1, 'whs', function ($join) {
//        $join->on('t1.Id', 'whs.Product');
//    })
//    ->select('t1.Name', 'whs.balance')
//    ->get();
//
//foreach($whs2 as $wh) {
//    echo $wh->Name . " " . $wh->balance . "\n";
//}

$datas = DB::table('ProductPrice')
    ->select( DB::raw('MAX(Id) as Id'))
    ->where('Product', 1)
    ->where('QuantityUnit', -1)
    ->where('Currency', -1)
    ->groupBy('Product', 'QuantityUnit', 'Currency')
    ->get();

echo count($datas);

echo $datas[0]->Id;


$datas = DB::table('ProductPrice as t5')->select(DB::raw('MAX(t5.Id) as Id'))
    ->where('t5.Product', 1)
    ->where('t5.QuantityUnit', -1)
    ->where('t5.PriceCategory', -1)
    ->where('t5.Currency', -1)
    ->groupBy('t5.Product', 't5.QuantityUnit', 't5.Currency', 't5.PriceCategory')
    ->get();


dd($datas[0]->Id);


$datas = DB::table('Product as t1')
    ->join('ProductPrice as t2', 't2.Product', '=', 't1.Id')
    ->leftJoin('ProductCategory as t3', 't3.Id', '=', 't1.ProductCategory' )
    ->leftJoin('QuantityUnit as t4', 't4.Id', '=', 't1.QuantityUnit')
    ->select('t1.Id', 't1.Code', 't1.Barcode', 't1.Name as ProductName', 't3.Name as ProductCategoryName',
        't4.Name as QuantityUnitName','t2.ValidFrom', 't2.Price')
    ->where('t1.Inactive', 0)
    ->where('t1.Service', 0)
    ->where('t1.Deleted', 0)
    ->where('t2.PriceCategory', -1)
    ->where('t2.Currency', -1)
    ->whereIn('t2.Id', function($query) {
        $query->from('ProductPrice as t5')->select(DB::raw('MAX(t5.Id) as Id'))
            ->where('t5.QuantityUnit', -1)
            ->where('t5.PriceCategory', -1)
            ->where('t5.Currency', -1)
            ->whereIn('t5.Product', function($query) {
                $query->from('Product as t6')->select('t6.Id')
                    ->where('t6.Inactive', 0)
                    ->where('t6.Service', 0)
                    ->where('t6.Deleted', 0)
                    ->get();
            })
            ->groupBy('t5.Product', 't5.QuantityUnit', 't5.Currency', 't5.PriceCategory')
            ->get();
    })
    ->get();

foreach($datas as $data) {
    echo $data->ProductName . " " . $data->Price . " " . $data->QuantityUnitName . "\n";
}
exit();
