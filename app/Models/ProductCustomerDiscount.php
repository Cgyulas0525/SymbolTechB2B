<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductCustomerDiscount
 *
 * @package App\Models
 * @version January 19, 2022, 9:52 am UTC
 * @property integer $Product
 * @property integer $Customer
 * @property number $Discount
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount whereCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCustomerDiscount whereProduct($value)
 * @mixin Model
 */
class ProductCustomerDiscount extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productcustomerdiscount';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Product',
        'Customer',
        'Discount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Product' => 'integer',
        'Customer' => 'integer',
        'Discount' => 'decimal:4'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Product' => 'required',
        'Customer' => 'required',
        'Discount' => 'required|numeric'
    ];


}
