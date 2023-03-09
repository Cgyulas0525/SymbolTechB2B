<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PaymentMethodLang
 *
 * @package App\Models
 * @version January 19, 2022, 9:47 am UTC
 * @property integer $Lang
 * @property integer $PaymentMethod
 * @property string $Name
 * @property string $VoucherComment
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethodLang whereVoucherComment($value)
 * @mixin Model
 */
class PaymentMethodLang extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'paymentmethodlang';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Lang',
        'PaymentMethod',
        'Name',
        'VoucherComment',
        'RowCreate',
        'RowModify'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Lang' => 'integer',
        'PaymentMethod' => 'integer',
        'Name' => 'string',
        'VoucherComment' => 'string',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Lang' => 'required|integer',
        'PaymentMethod' => 'required',
        'Name' => 'required|string|max:100',
        'VoucherComment' => 'nullable|string|max:65535',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
