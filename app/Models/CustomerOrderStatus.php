<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerOrderStatus
 *
 * @package App\Models
 * @version May 10, 2022, 8:02 am CEST
 * @property string $Name
 * @property integer $ForeColor
 * @property integer $BackColor
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus newQuery()
 * @method static \Illuminate\Database\Query\Builder|CustomerOrderStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereBackColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereForeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOrderStatus whereRowModify($value)
 * @method static \Illuminate\Database\Query\Builder|CustomerOrderStatus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CustomerOrderStatus withoutTrashed()
 * @mixin Model
 */
class CustomerOrderStatus extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'customerorderstatus';

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'ForeColor',
        'BackColor',
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
        'ForeColor' => 'integer',
        'BackColor' => 'integer',
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
        'ForeColor' => 'nullable|integer',
        'BackColor' => 'nullable|integer',
        'Deleted' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
