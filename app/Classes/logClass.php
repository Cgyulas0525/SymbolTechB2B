<?php
namespace App\Classes;

use App\Models\LogItem;
use App\Models\LogItemTable;
use DB;
use myUser;

Class logClass{

    public static function insertLogIn($type)
    {

        $id = DB::table('LogItem')->insertGetId(
            [   "customer_id" => session('customer_id'),
                "user_id" => myUser::user()->id,
                "eventtype" => $type,
                "eventdatetime" => now(),
                "remoteaddress" => '127.0.0.1'
            ]
        );

        return LogItem::find($id);

    }

    public static function insertDeleteRecord( $type, $tableName, $recordId)
    {
        $logItem = self::insertLogIn($type);

        $id = DB::table('LogItemTable')->insertGetId(
            [   "logitem_id" => $logItem->id,
                "tablename" => $tableName,
                "recordid" => $recordId,
            ]
        );

        return LogItemTable::find($id);
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
                        $logItemTable = self::insertDeleteRecord(6, $tableName, $new->id );
                        $headRecord = true;
                    }

                    $castsValue = $castsValues[array_search($keys[$i], $castsKeys)];

                    DB::table('LogItemTableDetail')->insert(
                        [
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
