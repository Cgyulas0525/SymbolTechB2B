<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerOffer
 *
 * @package App\Models
 * @version February 8, 2022, 3:07 pm UTC
 * @property integer $VoucherSequence
 * @property string $VoucherNumber
 * @property string $Name
 * @property string|\Carbon\Carbon $ValidFrom
 * @property string|\Carbon\Carbon $ValidTo
 * @property integer $OrderDlvFrom
 * @property integer $OrderDlvTo
 * @property integer $Campaign
 * @property integer $CustomerDepend
 * @property string|\Carbon\Carbon $RowVersion
 * @property int $Id
 * @property-read mixed $voucher_sequence_name
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereCustomerDepend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereOrderDlvFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereOrderDlvTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereValidTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereVoucherNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOffer whereVoucherSequence($value)
 * @mixin Model
 */
class CustomerOffer extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'customeroffer';
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'VoucherSequence',
        'VoucherNumber',
        'Name',
        'ValidFrom',
        'ValidTo',
        'OrderDlvFrom',
        'OrderDlvTo',
        'Campaign',
        'CustomerDepend',
        'RowVersion'
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
        'Name' => 'string',
        'ValidFrom' => 'datetime',
        'ValidTo' => 'datetime',
        'OrderDlvFrom' => 'integer',
        'OrderDlvTo' => 'integer',
        'Campaign' => 'integer',
        'CustomerDepend' => 'integer',
        'RowVersion' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'VoucherSequence' => 'required',
        'VoucherNumber' => 'required|string|max:100',
        'Name' => 'nullable|string|max:100',
        'ValidFrom' => 'required',
        'ValidTo' => 'required',
        'OrderDlvFrom' => 'required',
        'OrderDlvTo' => 'required',
        'Campaign' => 'nullable',
        'CustomerDepend' => 'required|integer',
        'RowVersion' => 'required'
    ];

    protected $append = ['voucherSequenceName'];

    public function getVoucherSequenceNameAttribute()
    {
        return !empty($this->VoucherSequence) ? VoucherSequence::find($this->VoucherSequence)->Name : '';
    }


}
