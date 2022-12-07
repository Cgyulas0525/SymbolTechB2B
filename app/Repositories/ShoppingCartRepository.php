<?php

namespace App\Repositories;

use App\Models\ShoppingCart;
use App\Repositories\BaseRepository;

/**
 * Class ShoppingCartRepository
 * @package App\Repositories
 * @version March 5, 2022, 4:58 pm CET
*/

class ShoppingCartRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'VoucherType',
        'VoucherSequence',
        'VoucherNumber',
        'PrimeVoucherNumber',
        'CancelledVoucher',
        'MaintenanceProduct',
        'Customer',
        'CustomerAddress',
        'CustomerContact',
        'VoucherDate',
        'DeliveryDate',
        'DeliveryFrom',
        'DeliveryTo',
        'PaymentMethod',
        'Currency',
        'CurrencyRate',
        'Investment',
        'Division',
        'Agent',
        'ContactEmployee',
        'Campaign',
        'CustomerContract',
        'Warehouse',
        'TransportMode',
        'DepositValue',
        'DepositPercent',
        'NetValue',
        'GrossValue',
        'VatValue',
        'AmountAsk',
        'Maintenance',
        'SplitForbid',
        'PrimePostage',
        'OrderHidePrice',
        'Closed',
        'ClosedManually',
        'Comment',
        'Cancelled',
        'RowVersion',
        'MaintOrderSrcCustOrder',
        'ExpirationDate',
        'InternalComment',
        'FinalizedDate',
        'ParcelShop',
        'StrExA',
        'StrExB',
        'StrExC',
        'StrExD',
        'DateExA',
        'DateExB',
        'DateExC',
        'DateExD',
        'NumExA',
        'NumExB',
        'NumExC',
        'NumExD',
        'BoolExA',
        'BoolExB',
        'BoolExC',
        'BoolExD',
        'LookupExA',
        'LookupExB',
        'LookupExC',
        'LookupExD',
        'MemoExA',
        'MemoExB',
        'MemoExC',
        'MemoExD',
        'RowCreate',
        'RowModify',
        'NotifyPhone',
        'NotifySms',
        'NotifyEmail',
        'PublicHealthPTFree',
        'FabricDeadLine',
        'CheckoutBankAccount',
        'OriginalVoucher',
        'DepositGross',
        'ExchangePackage',
        'ChainTransaction',
        'ValidityDate',
        'CurrRateDate',
        'Opened',
        'CustomerOrder'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ShoppingCart::class;
    }
}
