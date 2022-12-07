<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ProductAssociationType
 *
 * @package App\Models
 * @version January 19, 2022, 9:51 am UTC
 * @property string $Name
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAssociationType whereRowModify($value)
 * @mixin Model
 */
class ProductAssociationType extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'productassociationtype';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'Deleted',
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
        'Deleted' => 'integer',
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
        'Deleted' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
