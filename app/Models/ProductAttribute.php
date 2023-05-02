<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductAttribute
 *
 * @package App\Models
 * @version January 19, 2022, 9:51 am UTC
 * @property string $Name
 * @property integer $AttributeTypeValue
 * @property string $Postfix
 * @property integer $Filter
 * @property integer $HideFromVoucher
 * @property integer $Priority
 * @property integer $HideFromWeb
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereAttributeTypeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereHideFromVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereHideFromWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute wherePostfix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAttribute whereRowModify($value)
 * @mixin Model
 */
class ProductAttribute extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productattribute';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'AttributeTypeValue',
        'Postfix',
        'Filter',
        'HideFromVoucher',
        'Priority',
        'HideFromWeb',
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
        'Name' => 'string',
        'AttributeTypeValue' => 'integer',
        'Postfix' => 'string',
        'Filter' => 'integer',
        'HideFromVoucher' => 'integer',
        'Priority' => 'integer',
        'HideFromWeb' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Name' => 'required|string|max:100',
        'AttributeTypeValue' => 'required|integer',
        'Postfix' => 'nullable|string|max:8',
        'Filter' => 'required',
        'HideFromVoucher' => 'required',
        'Priority' => 'required|integer',
        'HideFromWeb' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];

    public function productAttributesRelation() {
        return $this->hasMany(ProductAttributes::class, 'ProductAttribute', 'Id');
    }
}
