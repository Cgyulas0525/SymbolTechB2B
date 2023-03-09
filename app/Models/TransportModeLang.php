<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TransportModeLang
 *
 * @package App\Models
 * @version January 19, 2022, 9:54 am UTC
 * @property integer $Lang
 * @property integer $TransportMode
 * @property string $Name
 * @property string $VoucherComment
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereTransportMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportModeLang whereVoucherComment($value)
 * @mixin Model
 */
class TransportModeLang extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'transportmodelang';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Lang',
        'TransportMode',
        'Name',
        'VoucherComment',
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
        'Lang' => 'integer',
        'TransportMode' => 'integer',
        'Name' => 'string',
        'VoucherComment' => 'string',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Lang' => 'required|integer',
        'TransportMode' => 'required',
        'Name' => 'required|string|max:100',
        'VoucherComment' => 'nullable|string|max:65535',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];


}
