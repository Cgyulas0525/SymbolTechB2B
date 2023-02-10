<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TransportMode
 *
 * @package App\Models
 * @version January 19, 2022, 9:54 am UTC
 * @property string $Name
 * @property number $DiscountPercent
 * @property string $VoucherComment
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @property string|null $Code
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomerAddress[] $CustomerAddress
 * @property-read int|null $customer_address_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomerBid[] $CustomerBid
 * @property-read int|null $customer_bid_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customer
 * @property-read int|null $customer_count
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportMode whereVoucherComment($value)
 * @mixin Model
 */
class TransportMode extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'transportmode';

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'DiscountPercent',
        'VoucherComment',
        'Deleted',
        'RowCreate',
        'RowModify',
        'Code',
        'Personal',
        'OwnDelivery',
        'ParcelCompany'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Name' => 'string',
        'DiscountPercent' => 'decimal:4',
        'VoucherComment' => 'string',
        'Deleted' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'Code' => 'string',
        'Personal' => 'integer',
        'OwnDelivery' => 'integer',
        'ParcelCompany' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Name' => 'required|string|max:100',
        'DiscountPercent' => 'required|numeric',
        'VoucherComment' => 'nullable|string|max:100',
        'Deleted' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];

    public function customer() {
        return $this->hasMany('App\Models\Customer', 'TransportMode');
    }

    public function CustomerAddress() {
        return $this->hasMany('App\Models\CustomerAddress', 'TransportMode');
    }

    public function CustomerBid() {
        return $this->hasMany('App\Models\CustomerBid', 'TransportMode');
    }


}
