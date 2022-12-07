<?php

namespace App\Repositories;

use App\Models\CustomerOrder;
use App\Repositories\BaseRepository;

/**
 * Class CustomerOrderRepository
 * @package App\Repositories
 * @version February 1, 2022, 3:05 pm UTC
*/

class CustomerOrderRepository extends BaseRepository
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
        'CancelReason',
        'CustomerOrderStatus',
        'BankTRID',
        'CloseReason'
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
        return CustomerOrder::class;
    }
}
