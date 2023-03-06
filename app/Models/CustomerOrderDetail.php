<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerOrderDetail
 *
 * @package App\Models
 * @version January 19, 2022, 9:45 am UTC
 * @property integer $CustomerOrder
 * @property integer $CancelledDetail
 * @property string|\Carbon\Carbon $DeliveryDate
 * @property string|\Carbon\Carbon $DeliveryFrom
 * @property string|\Carbon\Carbon $DeliveryTo
 * @property integer $Currency
 * @property number $CurrencyRate
 * @property integer $Investment
 * @property integer $Division
 * @property integer $Agent
 * @property integer $Campaign
 * @property integer $Product
 * @property string $ProductAlias
 * @property integer $MaintenanceProduct
 * @property string $Keywords
 * @property integer $Vat
 * @property integer $QuantityUnit
 * @property number $QURate
 * @property number $Quantity
 * @property number $FulfilledQuantity
 * @property number $CancelledQuantity
 * @property number $CompleteQuantity
 * @property integer $DetailStatus
 * @property integer $CustomerOfferDetail
 * @property integer $CustomerContractDetail
 * @property integer $AllocateWarehouse
 * @property integer $MustMunufacturing
 * @property number $ManufacQuantity
 * @property number $UnitPrice
 * @property number $DiscountPercent
 * @property number $DiscountUnitPrice
 * @property integer $GrossPrices
 * @property number $DepositValue
 * @property number $DepositPercent
 * @property number $NetValue
 * @property number $GrossValue
 * @property number $VatValue
 * @property string $Comment
 * @property integer $CopyCommentToInvoice
 * @property integer $RowOrder
 * @property string|\Carbon\Carbon $RowVersion
 * @property integer $Reverse
 * @property string $InternalComment
 * @property string $StrExA
 * @property string $StrExB
 * @property string $StrExC
 * @property string $StrExD
 * @property string|\Carbon\Carbon $DateExA
 * @property string|\Carbon\Carbon $DateExB
 * @property string|\Carbon\Carbon $DateExC
 * @property string|\Carbon\Carbon $DateExD
 * @property number $NumExA
 * @property number $NumExB
 * @property number $NumExC
 * @property number $NumExD
 * @property integer $BoolExA
 * @property integer $BoolExB
 * @property integer $BoolExC
 * @property integer $BoolExD
 * @property integer $LookupExA
 * @property integer $LookupExB
 * @property integer $LookupExC
 * @property integer $LookupExD
 * @property string $MemoExA
 * @property string $MemoExB
 * @property string $MemoExC
 * @property string $MemoExD
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property integer $FabricSchema
 * @property number $PublicHealthPTUPrice
 * @property string|\Carbon\Carbon $FabricDeadLine
 * @property integer $PriceCategory
 * @property string|\Carbon\Carbon $CurrRateDate
 * @property int $Id
 * @property-read mixed $currency_name
 * @property-read mixed $product_name
 * @property-read mixed $quantity_unit_name
 * @property-read mixed $status_name
 * @property-read mixed $vat_name
 * @property-read mixed $vat_rate
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereAllocateWarehouse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereBoolExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereBoolExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereBoolExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereBoolExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCancelledDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCancelledQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCompleteQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCopyCommentToInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCurrRateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCurrencyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCustomerContractDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCustomerOfferDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereCustomerOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDateExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDateExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDateExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDateExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDeliveryFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDeliveryTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDepositPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDepositValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDetailStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDiscountUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereFabricDeadLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereFabricSchema($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereFulfilledQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereGrossPrices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereGrossValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereInternalComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereLookupExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereLookupExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereLookupExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereLookupExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereMaintenanceProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereManufacQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereMemoExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereMemoExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereMemoExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereMemoExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereMustMunufacturing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereNumExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereNumExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereNumExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereNumExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail wherePriceCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereProductAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail wherePublicHealthPTUPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereQURate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereReverse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereRowOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereStrExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereStrExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereStrExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereStrExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderDetail whereVatValue($value)
 * @mixin Model
 */
class CustomerOrderDetail extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'customerorderdetail';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'CustomerOrder',
        'CancelledDetail',
        'DeliveryDate',
        'DeliveryFrom',
        'DeliveryTo',
        'Currency',
        'CurrencyRate',
        'Investment',
        'Division',
        'Agent',
        'Campaign',
        'Product',
        'ProductAlias',
        'MaintenanceProduct',
        'Keywords',
        'Vat',
        'QuantityUnit',
        'QURate',
        'Quantity',
        'FulfilledQuantity',
        'CancelledQuantity',
        'CompleteQuantity',
        'DetailStatus',
        'CustomerOfferDetail',
        'CustomerContractDetail',
        'AllocateWarehouse',
        'MustMunufacturing',
        'ManufacQuantity',
        'UnitPrice',
        'DiscountPercent',
        'DiscountUnitPrice',
        'GrossPrices',
        'DepositValue',
        'DepositPercent',
        'NetValue',
        'GrossValue',
        'VatValue',
        'Comment',
        'CopyCommentToInvoice',
        'RowOrder',
        'RowVersion',
        'Reverse',
        'InternalComment',
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
        'FabricSchema',
        'PublicHealthPTUPrice',
        'FabricDeadLine',
        'PriceCategory',
        'CurrRateDate',
        'StrExE',
        'StrExF',
        'StrExG',
        'StrExH',
        'StrExI',
        'StrExJ',
        'DateExE',
        'DateExF',
        'DateExG',
        'DateExH',
        'DateExI',
        'DateExJ',
        'NumExE',
        'NumExF',
        'NumExG',
        'NumExH',
        'NumExI',
        'NumExJ',
        'BoolExE',
        'BoolExF',
        'BoolExG',
        'BoolExH',
        'BoolExI',
        'BoolExJ',
        'LookupExE',
        'LookupExF',
        'LookupExG',
        'LookupExH',
        'LookupExI',
        'LookupExJ',
        'MemoExE',
        'MemoExF',
        'MemoExG',
        'MemoExH',
        'MemoExI',
        'MemoExJ',
        'RowPosition',
        'OriginalVoucher',
        'PickingNumber',
        'ParcelIdentifier',
        'SupplierDeliveryDate',
        'SupplierQuantity'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'CustomerOrder' => 'integer',
        'CancelledDetail' => 'integer',
        'DeliveryDate' => 'datetime',
        'DeliveryFrom' => 'datetime',
        'DeliveryTo' => 'datetime',
        'Currency' => 'integer',
        'CurrencyRate' => 'decimal:4',
        'Investment' => 'integer',
        'Division' => 'integer',
        'Agent' => 'integer',
        'Campaign' => 'integer',
        'Product' => 'integer',
        'ProductAlias' => 'string',
        'MaintenanceProduct' => 'integer',
        'Keywords' => 'string',
        'Vat' => 'integer',
        'QuantityUnit' => 'integer',
        'QURate' => 'decimal:4',
        'Quantity' => 'decimal:4',
        'FulfilledQuantity' => 'decimal:4',
        'CancelledQuantity' => 'decimal:4',
        'CompleteQuantity' => 'decimal:4',
        'DetailStatus' => 'integer',
        'CustomerOfferDetail' => 'integer',
        'CustomerContractDetail' => 'integer',
        'AllocateWarehouse' => 'integer',
        'MustMunufacturing' => 'integer',
        'ManufacQuantity' => 'decimal:4',
        'UnitPrice' => 'decimal:4',
        'DiscountPercent' => 'decimal:4',
        'DiscountUnitPrice' => 'decimal:4',
        'GrossPrices' => 'integer',
        'DepositValue' => 'decimal:4',
        'DepositPercent' => 'decimal:4',
        'NetValue' => 'decimal:4',
        'GrossValue' => 'decimal:4',
        'VatValue' => 'decimal:4',
        'Comment' => 'string',
        'CopyCommentToInvoice' => 'integer',
        'RowOrder' => 'integer',
        'RowVersion' => 'datetime',
        'Reverse' => 'integer',
        'InternalComment' => 'string',
        'StrExA' => 'string',
        'StrExB' => 'string',
        'StrExC' => 'string',
        'StrExD' => 'string',
        'DateExA' => 'datetime',
        'DateExB' => 'datetime',
        'DateExC' => 'datetime',
        'DateExD' => 'datetime',
        'NumExA' => 'decimal:4',
        'NumExB' => 'decimal:4',
        'NumExC' => 'decimal:4',
        'NumExD' => 'decimal:4',
        'BoolExA' => 'integer',
        'BoolExB' => 'integer',
        'BoolExC' => 'integer',
        'BoolExD' => 'integer',
        'LookupExA' => 'integer',
        'LookupExB' => 'integer',
        'LookupExC' => 'integer',
        'LookupExD' => 'integer',
        'MemoExA' => 'string',
        'MemoExB' => 'string',
        'MemoExC' => 'string',
        'MemoExD' => 'string',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'FabricSchema' => 'integer',
        'PublicHealthPTUPrice' => 'decimal:4',
        'FabricDeadLine' => 'datetime',
        'PriceCategory' => 'integer',
        'CurrRateDate' => 'datetime',
        'StrExE' => 'string',
        'StrExF' => 'string',
        'StrExG' => 'string',
        'StrExH' => 'string',
        'StrExI' => 'string',
        'StrExJ' => 'string',
        'DateExE' => 'datetime',
        'DateExF' => 'datetime',
        'DateExG' => 'datetime',
        'DateExH' => 'datetime',
        'DateExI' => 'datetime',
        'DateExJ' => 'datetime',
        'NumExE' => '',
        'NumExF' => '',
        'NumExG' => '',
        'NumExH' => '',
        'NumExI' => '',
        'NumExJ' => '',
        'BoolExE' => 'integer',
        'BoolExF' => 'integer',
        'BoolExG' => 'integer',
        'BoolExH' => 'integer',
        'BoolExI' => 'integer',
        'BoolExJ' => 'integer',
        'LookupExE' => 'integer',
        'LookupExF' => 'integer',
        'LookupExG' => 'integer',
        'LookupExH' => 'integer',
        'LookupExI' => 'integer',
        'LookupExJ' => 'integer',
        'MemoExE' => 'string',
        'MemoExF' => 'string',
        'MemoExG' => 'string',
        'MemoExH' => 'string',
        'MemoExI' => 'string',
        'MemoExJ' => 'string',
        'RowPosition' => '',
        'OriginalVoucher' => 'integer',
        'PickingNumber' => 'string',
        'ParcelIdentifier' => 'string',
        'SupplierDeliveryDate' => 'datetime',
        'SupplierQuantity' => 'decimal:4'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'CustomerOrder' => 'required',
        'CancelledDetail' => 'nullable',
        'DeliveryDate' => 'nullable',
        'DeliveryFrom' => 'nullable',
        'DeliveryTo' => 'nullable',
        'Currency' => 'required',
        'CurrencyRate' => 'required|numeric',
        'Investment' => 'nullable',
        'Division' => 'nullable',
        'Agent' => 'nullable',
        'Campaign' => 'nullable',
        'Product' => 'nullable',
        'ProductAlias' => 'nullable|string|max:100',
        'MaintenanceProduct' => 'nullable',
        'Keywords' => 'nullable|string|max:100',
        'Vat' => 'nullable',
        'QuantityUnit' => 'nullable',
        'QURate' => 'required|numeric',
        'Quantity' => 'required|numeric',
        'FulfilledQuantity' => 'required|numeric',
        'CancelledQuantity' => 'required|numeric',
        'CompleteQuantity' => 'required|numeric',
        'DetailStatus' => 'nullable',
        'CustomerOfferDetail' => 'nullable',
        'CustomerContractDetail' => 'nullable',
        'AllocateWarehouse' => 'required',
        'MustMunufacturing' => 'required',
        'ManufacQuantity' => 'required|numeric',
        'UnitPrice' => 'nullable|numeric',
        'DiscountPercent' => 'nullable|numeric',
        'DiscountUnitPrice' => 'nullable|numeric',
        'GrossPrices' => 'required',
        'DepositValue' => 'nullable|numeric',
        'DepositPercent' => 'nullable|numeric',
        'NetValue' => 'nullable|numeric',
        'GrossValue' => 'nullable|numeric',
        'VatValue' => 'nullable|numeric',
        'Comment' => 'nullable|string|max:65535',
        'CopyCommentToInvoice' => 'required',
        'RowOrder' => 'required|integer',
        'RowVersion' => 'required',
        'Reverse' => 'required',
        'InternalComment' => 'nullable|string|max:65535',
        'StrExA' => 'nullable|string|max:100',
        'StrExB' => 'nullable|string|max:100',
        'StrExC' => 'nullable|string|max:100',
        'StrExD' => 'nullable|string|max:100',
        'DateExA' => 'nullable',
        'DateExB' => 'nullable',
        'DateExC' => 'nullable',
        'DateExD' => 'nullable',
        'NumExA' => 'nullable|numeric',
        'NumExB' => 'nullable|numeric',
        'NumExC' => 'nullable|numeric',
        'NumExD' => 'nullable|numeric',
        'BoolExA' => 'required',
        'BoolExB' => 'required',
        'BoolExC' => 'required',
        'BoolExD' => 'required',
        'LookupExA' => 'nullable',
        'LookupExB' => 'nullable',
        'LookupExC' => 'nullable',
        'LookupExD' => 'nullable',
        'MemoExA' => 'nullable|string|max:65535',
        'MemoExB' => 'nullable|string|max:65535',
        'MemoExC' => 'nullable|string|max:65535',
        'MemoExD' => 'nullable|string|max:65535',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'FabricSchema' => 'nullable',
        'PublicHealthPTUPrice' => 'nullable|numeric',
        'FabricDeadLine' => 'nullable',
        'PriceCategory' => 'nullable',
        'CurrRateDate' => 'nullable'
    ];

    protected $append = [
        'ProductName',
        'QuantityUnitName',
        'CurrencyName',
        'VatName',
        'VatRate',
        'StatusName'
    ];

    public function getProductNameAttribute() {
        $product = Product::where('Id', $this->Product)->first();
        return !empty($this->Product) ? !empty($product) ? $product->Name : ' ' : ' ';
    }

    public function getQuantityUnitNameAttribute() {
        $quantityUnit = QuantityUnit::where('Id', $this->QuantityUnit)->first();
        return !empty($this->QuantityUnit) ? !empty($quantityUnit) ? $quantityUnit->Name : ' ' : ' ';
    }

    public function getCurrencyNameAttribute() {
        $currency = Currency::where('Id', $this->Currency)->first();
        return !empty($this->Currency) ? !empty($currency) ? $currency->Name : ' ' : ' ';
    }

    public function getVatNameAttribute() {
        $vat = Vat::where('Id', $this->Vat)->first();
        return !empty($this->Vat) ? !empty($vat) ? $vat->Name : ' ' : ' ';
    }

    public function getVatRateAttribute() {
        $vat = Vat::where('Id', $this->Vat)->first();
        return !empty($this->Vat) ? !empty($vat) ? $vat->Rate : 0 : 0;
    }

    public function getStatusNameAttribute() {
        $status = CustomerOrderDetailStatus::where('Id', $this->DetailStatus)->first();
        return !empty($this->DetailStatus) ? !empty($status) ? $status->Name : ' ' : ' ';
    }

}
