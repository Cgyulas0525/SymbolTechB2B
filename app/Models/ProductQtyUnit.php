<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductQtyUnit
 *
 * @package App\Models
 * @version January 19, 2022, 9:52 am UTC
 * @property integer $Product
 * @property integer $QuantityUnit
 * @property number $Multiplier
 * @property integer $QuantityDigits
 * @property integer $VoucherDisplay
 * @property integer $Commerce
 * @property integer $SellDefault
 * @property integer $BuyDefault
 * @property integer $SellBanned
 * @property integer $BuyBanned
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereBuyBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereBuyDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereCommerce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereMultiplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereQuantityDigits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereSellBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereSellDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQtyUnit whereVoucherDisplay($value)
 * @mixin Model
 */
class ProductQtyUnit extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productqtyunit';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Product',
        'QuantityUnit',
        'Multiplier',
        'QuantityDigits',
        'VoucherDisplay',
        'Commerce',
        'SellDefault',
        'BuyDefault',
        'SellBanned',
        'BuyBanned',
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
        'Product' => 'integer',
        'QuantityUnit' => 'integer',
        'Multiplier' => 'decimal:4',
        'QuantityDigits' => 'integer',
        'VoucherDisplay' => 'integer',
        'Commerce' => 'integer',
        'SellDefault' => 'integer',
        'BuyDefault' => 'integer',
        'SellBanned' => 'integer',
        'BuyBanned' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Product' => 'required',
        'QuantityUnit' => 'required',
        'Multiplier' => 'required|numeric',
        'QuantityDigits' => 'required|integer',
        'VoucherDisplay' => 'required',
        'Commerce' => 'required',
        'SellDefault' => 'required',
        'BuyDefault' => 'required',
        'SellBanned' => 'required',
        'BuyBanned' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
