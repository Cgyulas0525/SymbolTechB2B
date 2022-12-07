<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerContact
 *
 * @package App\Models
 * @version January 19, 2022, 9:45 am UTC
 * @property integer $Customer
 * @property integer $CustomerAddress
 * @property string $Name
 * @property integer $Theeing
 * @property string $Responsibility
 * @property string $Phone
 * @property string $Fax
 * @property string $Sms
 * @property string $Email
 * @property string $Url
 * @property string $Skype
 * @property string $FacebookUrl
 * @property string $Msn
 * @property string $Comment
 * @property string $VoucherComment
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowVersion
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property int $Id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomerBid[] $CustomerBid
 * @property-read int|null $customer_bid_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereMsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereResponsibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereSkype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereTheeing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereVoucherComment($value)
 * @mixin Model
 */
class CustomerContact extends Model
{
//    // use SoftDeletes;

    use HasFactory;

    public $table = 'customercontact';

//    // const CREATED_AT = 'created_at';
//    // const UPDATED_AT = 'updated_at';
//
//
//    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Customer',
        'CustomerAddress',
        'Name',
        'Theeing',
        'Responsibility',
        'Phone',
        'Fax',
        'Sms',
        'Email',
        'Url',
        'Skype',
        'FacebookUrl',
        'Msn',
        'Comment',
        'VoucherComment',
        'Deleted',
        'RowVersion',
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
        'Customer' => 'integer',
        'CustomerAddress' => 'integer',
        'Name' => 'string',
        'Theeing' => 'integer',
        'Responsibility' => 'string',
        'Phone' => 'string',
        'Fax' => 'string',
        'Sms' => 'string',
        'Email' => 'string',
        'Url' => 'string',
        'Skype' => 'string',
        'FacebookUrl' => 'string',
        'Msn' => 'string',
        'Comment' => 'string',
        'VoucherComment' => 'string',
        'Deleted' => 'integer',
        'RowVersion' => 'datetime',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Customer' => 'required',
        'CustomerAddress' => 'nullable',
        'Name' => 'required|string|max:100',
        'Theeing' => 'required',
        'Responsibility' => 'nullable|string|max:100',
        'Phone' => 'nullable|string|max:20',
        'Fax' => 'nullable|string|max:20',
        'Sms' => 'nullable|string|max:20',
        'Email' => 'nullable|string|max:100',
        'Url' => 'nullable|string|max:100',
        'Skype' => 'nullable|string|max:10',
        'FacebookUrl' => 'nullable|string|max:100',
        'Msn' => 'nullable|string|max:100',
        'Comment' => 'nullable|string|max:65535',
        'VoucherComment' => 'nullable|string|max:65535',
        'Deleted' => 'required',
        'RowVersion' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable'
    ];

    public function CustomerData() {
        return $this->belongsTo('App\Models\Customer', 'Customer', 'Id');
    }

    public function customercontactfavoriteproduct() {
        $this->hasMany(CustomerContactFavoriteProduct::class, 'customercontact_id', 'Id');
    }

}
