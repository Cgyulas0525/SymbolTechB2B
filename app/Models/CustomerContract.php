<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerContract
 *
 * @package App\Models
 * @version February 22, 2022, 10:53 am CET
 * @property integer $Id
 * @property integer $VoucherSequence
 * @property string $VoucherNumber
 * @property string $PrimeVoucherNumber
 * @property integer $Customer
 * @property integer $AddressDepends
 * @property integer $CustomerAddress
 * @property string $Subject
 * @property string|\Carbon\Carbon $ValidFrom
 * @property string|\Carbon\Carbon $ValidTo
 * @property string $InvoiceOccurence
 * @property string|\Carbon\Carbon $AlertGenerated
 * @property integer $PaymentMethod
 * @property integer $SuppressPriceAffect
 * @property integer $OfferOverride
 * @property integer $ManualAdapt
 * @property string $Comment
 * @property integer $CopyCommentToInvoice
 * @property integer $Cancelled
 * @property string|\Carbon\Carbon $RowVersion
 * @property integer $InvoiceModeSeason
 * @property integer $Investment
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereAddressDepends($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereAlertGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereCopyCommentToInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereInvoiceModeSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereInvoiceOccurence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereManualAdapt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereOfferOverride($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract wherePrimeVoucherNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereSuppressPriceAffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereValidTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereVoucherNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContract whereVoucherSequence($value)
 * @mixin Model
 */
class CustomerContract extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'customercontract';

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'Id',
        'VoucherSequence',
        'VoucherNumber',
        'PrimeVoucherNumber',
        'Customer',
        'AddressDepends',
        'CustomerAddress',
        'Subject',
        'ValidFrom',
        'ValidTo',
        'InvoiceOccurence',
        'AlertGenerated',
        'PaymentMethod',
        'SuppressPriceAffect',
        'OfferOverride',
        'ManualAdapt',
        'Comment',
        'CopyCommentToInvoice',
        'Cancelled',
        'RowVersion',
        'InvoiceModeSeason',
        'Investment'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'VoucherSequence' => 'integer',
        'VoucherNumber' => 'string',
        'PrimeVoucherNumber' => 'string',
        'Customer' => 'integer',
        'AddressDepends' => 'integer',
        'CustomerAddress' => 'integer',
        'Subject' => 'string',
        'ValidFrom' => 'datetime',
        'ValidTo' => 'datetime',
        'InvoiceOccurence' => 'string',
        'AlertGenerated' => 'datetime',
        'PaymentMethod' => 'integer',
        'SuppressPriceAffect' => 'integer',
        'OfferOverride' => 'integer',
        'ManualAdapt' => 'integer',
        'Comment' => 'string',
        'CopyCommentToInvoice' => 'integer',
        'Cancelled' => 'integer',
        'RowVersion' => 'datetime',
        'InvoiceModeSeason' => 'integer',
        'Investment' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Id' => 'required',
        'VoucherSequence' => 'required',
        'VoucherNumber' => 'required|string|max:100',
        'PrimeVoucherNumber' => 'nullable|string|max:100',
        'Customer' => 'required',
        'AddressDepends' => 'required',
        'CustomerAddress' => 'nullable',
        'Subject' => 'nullable|string|max:100',
        'ValidFrom' => 'required',
        'ValidTo' => 'nullable',
        'InvoiceOccurence' => 'nullable|string',
        'AlertGenerated' => 'nullable',
        'PaymentMethod' => 'nullable',
        'SuppressPriceAffect' => 'required',
        'OfferOverride' => 'required',
        'ManualAdapt' => 'required',
        'Comment' => 'nullable|string',
        'CopyCommentToInvoice' => 'required',
        'Cancelled' => 'required',
        'RowVersion' => 'required',
        'InvoiceModeSeason' => 'required',
        'Investment' => 'nullable'
    ];


}
