<?php
namespace App\Classes;

use App\Models\Customer;

Class customerClass{

    /*
     * Adott partner DebitQuota mező adott értéke
     *
     * @param $id integer
     * @param $mit string
     *
     * @return $maxDebit integer
    */
    public static function CustomerDebitQuotaValue($id, $mit)
    {
        $maxDebit = 0;
        $customer = Customer::where('Id', $id)->first();
        if (!empty($customer)) {
            if (!empty($customer->DebitQuota)) {
                $maxDebit = utilityClass::XMLValue( utilityClass::XMLArray($customer->DebitQuota), $mit);
            }
        }
        return $maxDebit;
    }
}
