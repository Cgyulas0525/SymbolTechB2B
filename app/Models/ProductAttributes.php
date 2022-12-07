<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductAttributes
 *
 * @package App\Models
 * @version January 19, 2022, 9:51 am UTC
 * @property integer $Product
 * @property integer $ProductAttribute
 * @property string $ValueString
 * @property number $ValueDecimal
 * @property string|\Carbon\Carbon $ValueDate
 * @property integer $ValueBool
 * @property integer $ValueLookup
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereProductAttribute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereValueBool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereValueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereValueDecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereValueLookup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttributes whereValueString($value)
 * @mixin Model
 */
class ProductAttributes extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productattributes';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Product',
        'ProductAttribute',
        'ValueString',
        'ValueDecimal',
        'ValueDate',
        'ValueBool',
        'ValueLookup',
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
        'ProductAttribute' => 'integer',
        'ValueString' => 'string',
        'ValueDecimal' => 'decimal:4',
        'ValueDate' => 'datetime',
        'ValueBool' => 'integer',
        'ValueLookup' => 'integer',
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
        'ProductAttribute' => 'required',
        'ValueString' => 'required|string|max:100',
        'ValueDecimal' => 'required|numeric',
        'ValueDate' => 'nullable',
        'ValueBool' => 'required',
        'ValueLookup' => 'nullable',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
