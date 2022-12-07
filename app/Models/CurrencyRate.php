<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CurrencyRate
 *
 * @package App\Models
 * @version January 19, 2022, 9:34 am UTC
 * @property integer $Currency
 * @property string|\Carbon\Carbon $ValidFrom
 * @property number $Rate
 * @property number $RateBuy
 * @property number $RateSell
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @property-read \App\Models\Currency $CurrencyAdat
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereRateBuy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereRateSell($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CurrencyRate whereValidFrom($value)
 * @mixin Model
 */
class CurrencyRate extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'currencyrate';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Currency',
        'ValidFrom',
        'Rate',
        'RateBuy',
        'RateSell',
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
        'Currency' => 'integer',
        'ValidFrom' => 'datetime',
        'Rate' => 'decimal:4',
        'RateBuy' => 'decimal:4',
        'RateSell' => 'decimal:4',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Currency' => 'required',
        'ValidFrom' => 'required',
        'Rate' => 'required|numeric',
        'RateBuy' => 'nullable|numeric',
        'RateSell' => 'nullable|numeric',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];

    public function CurrencyAdat() {
        return $this->belongsTo('App\Models\Currency', 'Currency');
    }


}


//pl:
//$datas = \App\Models\CurrencyRate::with('CurrencyAdat:Id,Name')->first();
//
//dd($datas);
