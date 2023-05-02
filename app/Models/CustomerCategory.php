<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerCategory
 *
 * @package App\Models
 * @version January 19, 2022, 9:44 am UTC
 * @property string $Name
 * @property integer $Parent
 * @property integer $LeftValue
 * @property integer $RightValue
 * @property number $DiscountPercent
 * @property integer $PaymentMethod
 * @property integer $PaymentMethodStrict
 * @property integer $PriceCategory
 * @property integer $Currency
 * @property integer $TransportMode
 * @property string $VoucherRules
 * @property string $DebitQuota
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property integer $IsCompany
 * @property int $Id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customer
 * @property-read int|null $customer_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereDebitQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereIsCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereLeftValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory wherePaymentMethodStrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory wherePriceCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereRightValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereTransportMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereVoucherRules($value)
 * @mixin Model
 */
class CustomerCategory extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'customercategory';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'Parent',
        'LeftValue',
        'RightValue',
        'DiscountPercent',
        'PaymentMethod',
        'PaymentMethodStrict',
        'PriceCategory',
        'Currency',
        'TransportMode',
        'VoucherRules',
        'DebitQuota',
        'RowCreate',
        'RowModify',
        'IsCompany'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Name' => 'string',
        'Parent' => 'integer',
        'LeftValue' => 'integer',
        'RightValue' => 'integer',
        'DiscountPercent' => 'decimal:4',
        'PaymentMethod' => 'integer',
        'PaymentMethodStrict' => 'integer',
        'PriceCategory' => 'integer',
        'Currency' => 'integer',
        'TransportMode' => 'integer',
        'VoucherRules' => 'string',
        'DebitQuota' => 'string',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'IsCompany' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Name' => 'required|string|max:100',
        'Parent' => 'nullable',
        'LeftValue' => 'required',
        'RightValue' => 'required',
        'DiscountPercent' => 'nullable|numeric',
        'PaymentMethod' => 'nullable',
        'PaymentMethodStrict' => 'required',
        'PriceCategory' => 'nullable',
        'Currency' => 'nullable',
        'TransportMode' => 'nullable',
        'VoucherRules' => 'nullable|string|max:65535',
        'DebitQuota' => 'nullable|string|max:65535',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'IsCompany' => 'nullable'
    ];

    public function customerRelation() {
        return $this->hasMany(Customer::class, 'CustomerCategory', 'Id');
    }

    public function customerOfferCustomerRelation() {
        return $this->hasMany(CustomerOfferCustomer::class, 'CustomerCategory', 'Id');
    }



}
