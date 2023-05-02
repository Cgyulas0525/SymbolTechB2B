<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerContractDetail
 *
 * @package App\Models
 * @version February 22, 2022, 10:53 am CET
 * @property integer $Id
 * @property integer $CustomerContract
 * @property integer $Product
 * @property number $ShareQuantity
 * @property number $Price
 * @property integer $Currency
 * @property integer $QuantityUnit
 * @property number $InvoiceQty
 * @property integer $Vat
 * @property string|\Carbon\Carbon $ValidFrom
 * @property string|\Carbon\Carbon $ValidTo
 * @property string $InvoiceOccurence
 * @property integer $SuppressPriceAffect
 * @property integer $OfferOverride
 * @property string $Comment
 * @property integer $CopyCommentToInvoice
 * @property string|\Carbon\Carbon $RowVersion
 * @property integer $Deleted
 * @property integer $RowOrder
 * @property integer $Investment
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereCopyCommentToInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereCustomerContract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereInvoiceOccurence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereInvoiceQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereOfferOverride($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereRowOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereShareQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereSuppressPriceAffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereValidTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContractDetail whereVat($value)
 * @mixin Model
 */
class CustomerContractDetail extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'customercontractdetail';
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'Id',
        'CustomerContract',
        'Product',
        'ShareQuantity',
        'Price',
        'Currency',
        'QuantityUnit',
        'InvoiceQty',
        'Vat',
        'ValidFrom',
        'ValidTo',
        'InvoiceOccurence',
        'SuppressPriceAffect',
        'OfferOverride',
        'Comment',
        'CopyCommentToInvoice',
        'RowVersion',
        'Deleted',
        'RowOrder',
        'Investment'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'CustomerContract' => 'integer',
        'Product' => 'integer',
        'ShareQuantity' => 'decimal:4',
        'Price' => 'decimal:4',
        'Currency' => 'integer',
        'QuantityUnit' => 'integer',
        'InvoiceQty' => 'decimal:4',
        'Vat' => 'integer',
        'ValidFrom' => 'datetime',
        'ValidTo' => 'datetime',
        'InvoiceOccurence' => 'string',
        'SuppressPriceAffect' => 'integer',
        'OfferOverride' => 'integer',
        'Comment' => 'string',
        'CopyCommentToInvoice' => 'integer',
        'RowVersion' => 'datetime',
        'Deleted' => 'integer',
        'RowOrder' => 'integer',
        'Investment' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Id' => 'required',
        'CustomerContract' => 'required',
        'Product' => 'required',
        'ShareQuantity' => 'nullable|numeric',
        'Price' => 'required|numeric',
        'Currency' => 'required',
        'QuantityUnit' => 'nullable',
        'InvoiceQty' => 'required|numeric',
        'Vat' => 'required',
        'ValidFrom' => 'required',
        'ValidTo' => 'nullable',
        'InvoiceOccurence' => 'nullable|string',
        'SuppressPriceAffect' => 'required',
        'OfferOverride' => 'required',
        'Comment' => 'nullable|string',
        'CopyCommentToInvoice' => 'required',
        'RowVersion' => 'required',
        'Deleted' => 'required',
        'RowOrder' => 'required|integer',
        'Investment' => 'nullable'
    ];

    public function customerOrderDetailRelation() {
        return $this->belongsTo(CustomerOrderDetail::class, 'CustomerContractDetail', 'Id');
    }

    public function customerContractRelation() {
        return $this->belongsTo(CustomerContract::class, 'CustomerContract', 'Id');
    }

    public function productRelation() {
        return $this->belongsTo(Product::class, 'Product', 'Id');
    }

    public function currencyRelation() {
        return $this->belongsTo(Currency::class, 'Currency', 'Id');
    }

    public function quantityUnitRelation() {
        return $this->belongsTo(QuantityUnit::class, 'QuantityUnit', 'Id');
    }

    public function vatRelation() {
        return $this->belongsTo(Vat::class, 'Vat', 'Id');
    }

}
