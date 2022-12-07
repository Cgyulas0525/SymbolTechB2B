<?php
namespace App\Classes;

use DB;
use myUser;
use Auth;

Class SUAdatokClass{

    public static function GetCountComissionList() {

        $data = DB::connection('firebird')->table('OrderComission as oc')
                  ->join('Employee as e', 'e.Id', '=', 'oc.Employee')
                  ->select('oc.Id', 'oc.Name as ocName', 'oc.VoucherNumber', 'e.Name as eName',
                      DB::raw('(SELECT Count(1)
                                        FROM "OrderComissionDetail" as "ocd"
                                        INNER JOIN "CustomerOrderDetail" as "cod" ON "cod"."Id" = "ocd"."CustomerOrderDetail"
                                        INNER JOIN "Product" as "p" ON "p"."Id" = "cod"."Product"
                                       WHERE "ocd"."OrderComission" = "oc"."Id"
                                         AND "p"."Service" = 0) as OrderCount'),
                      DB::raw('(SELECT Count(1)
                                        FROM "OrderComissionDetail" as "ocd"
                                        INNER JOIN "CustomerOrderDetail" as "cod" ON "cod"."Id" = "ocd"."CustomerOrderDetail"
                                        INNER JOIN "CustomerOrder" as "co" ON "co"."Id" = "cod"."CustomerOrder"
                                        INNER JOIN "Product" as "p" ON "p"."Id" = "cod"."Product"
                                       WHERE "ocd"."OrderComission" = "oc"."Id"
                                         AND "co"."StrExA" IS NULL
                                         AND "p"."Service" = 0) as OpenOrderCount'))
                  ->whereNUll('oc.CloseDateTime')
                  ->orderBy(DB::raw('case when "oc"."Employee" = '.myUser::user()->Id.'THEN 1 ELSE 2 END, "oc"."Id"'))
                  ->get();

        return $data;

    }

}


