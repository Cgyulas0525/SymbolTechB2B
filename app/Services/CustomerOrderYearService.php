<?php

namespace App\Services;

use DB;

class CustomerOrderYearService
{
    public static function handle() {

        return DB::table('customerorder')->select(DB::raw('year(VoucherDate) as year'))
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')->toArray();

    }
}
