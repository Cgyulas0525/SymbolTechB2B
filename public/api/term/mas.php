<?php

require_once "Database.php";
require_once "ModelChange.php";

$sql = "SELECT * FROM shoppingcart WHERE Opened = 0 AND CustomerOrder IS NULL AND deleted_at IS NULL AND Customer = " . $_GET['Customer'];
$smtp = DB::run($sql);

if ($smtp) {
    $record = $smtp->fetchAll();
    if (count($record) > 0) {
        $scCastArray = ModelChange::modelExchange(ModelChange::modelRead('shoppingcart'));
        dd($scCastArray);

        $detailModelArray = ModelChange::modelRead('shoppingcartdetail');
        $array = [];
        foreach ($record as $row) {
            $detailSql = "SELECT * FROM shoppingcartdetail WHERE deleted_at IS NULL AND ShoppingCart = " . $row['Id'];
            $detailSmtp = DB::run($detailSql);
            $detailRecords = $detailSmtp->fetchAll();
            if ( count($detailRecords) > 0 ){
                $detailArray = [];
                foreach ( $detailRecords as $detailRecord ) {
                    $detailItem = array(
                        'ShoppingCart'          => $detailRecord['ShoppingCart'],
                        'Currency'              => $detailRecord['Currency'],
                        'CurrencyRate'          => $detailRecord['CurrencyRate'],
                        'Product'               => $detailRecord['Product'],
                        'Vat'                   => $detailRecord['Vat'],
                        'QuantityUnit'          => $detailRecord['QuantityUnit'],
                        'Reverse'               => $detailRecord['Reverse'],
                        'Quantity'              => $detailRecord['Quantity'],
                        'CustomerOfferDetail'   => $detailRecord['CustomerOfferDetail'],
                        'CustomerContractDetail'=> $detailRecord['CustomerContractDetail'],
                        'UnitPrice'             => $detailRecord['UnitPrice'],
                        'DiscountPercent'       => $detailRecord['DiscountPercent'],
                        'DiscountUnitPrice'     => $detailRecord['DiscountUnitPrice'],
                        'GrossPrices'           => $detailRecord['GrossPrices'],
                        'DepositValue'          => $detailRecord['DepositValue'],
                        'DepositPercent'        => $detailRecord['DepositPercent'],
                        'NetValue'              => $detailRecord['NetValue'],
                        'GrossValue'            => $detailRecord['GrossValue'],
                        'VatValue'              => $detailRecord['VatValue'],
                        'Comment'               => $detailRecord['Comment']
                    );
                    array_push($detailArray, $detailItem);
                }
            }
            $item = array(
                'Id'                 => $row['Id'],
                'VoucherNumber'      => $row['VoucherNumber'],
                'Customer'           => $row['Customer'],
                'CustomerAddress'    => $row['CustomerAddress'],
                'CustomerContact'    => $row['CustomerContact'],
                'VoucherDate'        => $row['VoucherDate'],
                'DeliveryDate'       => $row['DeliveryDate'],
                'PaymentMethod'      => $row['PaymentMethod'],
                'Currency'           => $row['Currency'],
                'CurrencyRate'       => $row['CurrencyRate'],
                'CustomerContract'   => $row['CustomerContract'],
                'TransportMode'      => $row['TransportMode'],
                'DepositValue'       => $row['DepositValue'],
                'DepositPercent'     => $row['DepositPercent'],
                'NetValue'           => $row['NetValue'],
                'GrossValue'         => $row['GrossValue'],
                'VatValue'           => $row['VatValue'],
                'Comment'            => $row['Comment'],
                'Details'            => count($detailArray) > 0 ? $detailArray : NULL);
            array_push($array, $item);
        }
    }
    $json_response = json_encode($array);
    echo $json_response;

} else {
    echo "Nincs új kosár!";
}
