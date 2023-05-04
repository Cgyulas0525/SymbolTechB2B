<?php

namespace App\Services;

use myUser;

class CustomerOrderContactService
{

    public static function contactSelect() {
        return [myUser::user()->name, myUser::user()->customerName];
    }

    public static function getContactName($tf) {
        return ($tf === 0) ? myUser::user()->name : myUser::user()->customerName;
    }

}
