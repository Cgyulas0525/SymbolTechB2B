<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductAssociation
 *
 * @package App\Models
 * @version January 19, 2022, 9:50 am UTC
 * @property integer $OriginalProduct
 * @property integer $AssociatedProduct
 * @property integer $ProductAssociationType
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation whereAssociatedProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation whereOriginalProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation whereProductAssociationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociation whereRowModify($value)
 * @mixin Model
 */
class ProductAssociation extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productassociation';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'OriginalProduct',
        'AssociatedProduct',
        'ProductAssociationType',
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
        'OriginalProduct' => 'integer',
        'AssociatedProduct' => 'integer',
        'ProductAssociationType' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'OriginalProduct' => 'required',
        'AssociatedProduct' => 'required',
        'ProductAssociationType' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
