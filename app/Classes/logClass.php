<?php
namespace App\Classes;

use App\Models\LogItem;
use App\Models\LogItemTable;
use App\Models\LogItemTableDetail;
use DB;
use App\Models\VoucherSequence;
use App\Models\CustomerOrder;
use App\Classes\utilityClass;
use Carbon\Carbon;
use function Symfony\Component\String\b;

Class logClass{

    public static function insertLogIn($type)
    {
        return LogItem::create([
            "customer_id" => session('customer_id'),
            "user_id" => session('user_id'),
            "eventtype" => $type,
            "eventdatetime" => now(),
            "remoteaddress" => '127.0.0.1'
        ]);
    }

    public static function insertDeleteRecord( $type, $tableName, $recordId)
    {
        $logItem = logClass::insertLogIn($type);

        return LogItemTable::create([
            "logitem_id" => $logItem->id,
            "tablename" => $tableName,
            "recordid" => $recordId,
        ]);
    }

    public static function modifyRecord( $tableName, $old, $new)
    {
        $newValues  = array_values($new->getAttributes());
        $oldValues  = array_values($old->getAttributes());
        $keys       = array_keys($new->getAttributes());
        $castsKeys   = array_keys($new->getCasts());
        $castsValues = array_values($new->getCasts());

        $headRecord = false;

        for ( $i = 0; $i < count($keys); $i++) {
            $pos = strpos($keys[$i], '_at');
            if ( empty($pos) ) {
                if ( $newValues[$i] != $oldValues[$i] ) {
                    if ( $headRecord == false ) {
                        $logItemTable = logClass::insertDeleteRecord(6, $tableName, $new->id );
                        $headRecord = true;
                    }

                    $castsValue = $castsValues[array_search($keys[$i], $castsKeys)];


                    LogItemTableDetail::create([
                       "logitemtable_id" => $logItemTable->id,
                        "changedfield" => $keys[$i],
                        "oldinteger" => $castsValue == "integer" ? $oldValues[$i] : NULL,
                        "newinteger" => $castsValue == "integer" ? $newValues[$i] : NULL,
                        "oldstring" => $castsValue == "string" ? $oldValues[$i] : NULL,
                        "newstring" => $castsValue == "string" ? $newValues[$i] : NULL,
                        "olddecimal" => $castsValue == "decimal" ? $oldValues[$i] : NULL,
                        "newdecimal" => $castsValue == "decimal" ? $newValues[$i] : NULL,
                        "olddatetime" => $castsValue == "datetime" ? $oldValues[$i] : NULL,
                        "newdatetime" => $castsValue == "datetime" ? $newValues[$i] : NULL,
                    ]);
                }
            }
        }

    }
}
