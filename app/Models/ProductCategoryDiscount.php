<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductCategoryDiscount
 * @package App\Models
 * @version March 1, 2023, 7:19 am CET
 *
 * @property integer $ProductCategory
 * @property integer $Customer
 * @property integer $CustCategory
 * @property integer $Inherit
 * @property number $Discount
 * @property string|\Carbon\Carbon $ValidFrom
 * @property string|\Carbon\Carbon $ValidTo
 */
class ProductCategoryDiscount extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'productcategorydiscount';
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'ProductCategory',
        'Customer',
        'CustCategory',
        'Inherit',
        'Discount',
        'ValidFrom',
        'ValidTo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'ProductCategory' => 'integer',
        'Customer' => 'integer',
        'CustCategory' => 'integer',
        'Inherit' => 'integer',
        'Discount' => 'decimal:4',
        'ValidFrom' => 'datetime',
        'ValidTo' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'ProductCategory' => 'required',
        'Customer' => 'nullable',
        'CustCategory' => 'nullable',
        'Inherit' => 'required',
        'Discount' => 'required|numeric',
        'ValidFrom' => 'nullable',
        'ValidTo' => 'nullable'
    ];


}
