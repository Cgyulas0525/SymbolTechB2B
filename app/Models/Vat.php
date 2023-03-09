<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Vat
 *
 * @package App\Models
 * @version January 19, 2022, 9:54 am UTC
 * @property integer $DirectionBuy
 * @property string $Name
 * @property number $Rate
 * @property number $ExpenseRate
 * @property integer $Converse
 * @property string $ConverseText
 * @property integer $Eu
 * @property integer $CashRegIndex
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property string $Description
 * @property integer $ShowDetailName
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|Vat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereCashRegIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereConverse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereConverseText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereDirectionBuy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereEu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereExpenseRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vat whereShowDetailName($value)
 * @mixin Model
 */
class Vat extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'vat';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'DirectionBuy',
        'Name',
        'Rate',
        'ExpenseRate',
        'Converse',
        'ConverseText',
        'Eu',
        'CashRegIndex',
        'Deleted',
        'RowCreate',
        'RowModify',
        'Description',
        'ShowDetailName'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'DirectionBuy' => 'integer',
        'Name' => 'string',
        'Rate' => 'decimal:4',
        'ExpenseRate' => 'decimal:4',
        'Converse' => 'integer',
        'ConverseText' => 'string',
        'Eu' => 'integer',
        'CashRegIndex' => 'integer',
        'Deleted' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'Description' => 'string',
        'ShowDetailName' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'DirectionBuy' => 'required',
        'Name' => 'required|string|max:100',
        'Rate' => 'required|numeric',
        'ExpenseRate' => 'nullable|numeric',
        'Converse' => 'required',
        'ConverseText' => 'nullable|string|max:100',
        'Eu' => 'required',
        'CashRegIndex' => 'required|integer',
        'Deleted' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'Description' => 'nullable|string|max:100',
        'ShowDetailName' => 'required'
    ];


}
