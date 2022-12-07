<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductAttachment
 *
 * @package App\Models
 * @version January 19, 2022, 9:51 am UTC
 * @property integer $Product
 * @property integer $AttachedProduct
 * @property number $Quantity
 * @property integer $QuantityUnit
 * @property number $UnitPrice
 * @property integer $Multiplied
 * @property integer $Sell
 * @property integer $Buy
 * @property integer $InlandOnly
 * @property integer $Maintenance
 * @property integer $CustomerCategory
 * @property integer $Once
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property integer $StockExchange
 * @property integer $SupplierOrderGenerate
 * @property integer $InternalOrderGenerate
 * @property integer $IntOrder
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereAttachedProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereBuy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereCustomerCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereInlandOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereIntOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereInternalOrderGenerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereMultiplied($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereOnce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereSell($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereStockExchange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereSupplierOrderGenerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttachment whereUnitPrice($value)
 * @mixin Model
 */
class ProductAttachment extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productattachment';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Product',
        'AttachedProduct',
        'Quantity',
        'QuantityUnit',
        'UnitPrice',
        'Multiplied',
        'Sell',
        'Buy',
        'InlandOnly',
        'Maintenance',
        'CustomerCategory',
        'Once',
        'RowCreate',
        'RowModify',
        'StockExchange',
        'SupplierOrderGenerate',
        'InternalOrderGenerate',
        'IntOrder'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Product' => 'integer',
        'AttachedProduct' => 'integer',
        'Quantity' => 'decimal:4',
        'QuantityUnit' => 'integer',
        'UnitPrice' => 'decimal:4',
        'Multiplied' => 'integer',
        'Sell' => 'integer',
        'Buy' => 'integer',
        'InlandOnly' => 'integer',
        'Maintenance' => 'integer',
        'CustomerCategory' => 'integer',
        'Once' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'StockExchange' => 'integer',
        'SupplierOrderGenerate' => 'integer',
        'InternalOrderGenerate' => 'integer',
        'IntOrder' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Product' => 'required',
        'AttachedProduct' => 'required',
        'Quantity' => 'required|numeric',
        'QuantityUnit' => 'nullable',
        'UnitPrice' => 'nullable|numeric',
        'Multiplied' => 'required',
        'Sell' => 'required',
        'Buy' => 'required',
        'InlandOnly' => 'required',
        'Maintenance' => 'required',
        'CustomerCategory' => 'nullable',
        'Once' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'StockExchange' => 'required',
        'SupplierOrderGenerate' => 'required',
        'InternalOrderGenerate' => 'required',
        'IntOrder' => 'required'
    ];


}
