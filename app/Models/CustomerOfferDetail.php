<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Product;
use App\Models\CustomerOffer;
use App\Models\Currency;

/**
 * Class CustomerOfferDetail
 *
 * @package App\Models
 * @version February 8, 2022, 3:07 pm UTC
 * @property integer $CustomerOffer
 * @property integer $Product
 * @property number $ShareQuantity
 * @property integer $Currency
 * @property integer $QuantityUnit
 * @property number $SalesPrice
 * @property number $QuantityMinimum
 * @property number $QuantityMaximum
 * @property integer $SupplierOfferDetail
 * @property string|\Carbon\Carbon $RowVersion
 * @property integer $RowOrder
 * @property int $Id
 * @property-read mixed $currency_name
 * @property-read mixed $customer_offer_name
 * @property-read mixed $customer_offer_valid_from
 * @property-read mixed $customer_offer_valid_to
 * @property-read mixed $customer_offer_voucher_number
 * @property-read mixed $product_name
 * @property-read mixed $product_picture
 * @property-read mixed $quantity_unit_name
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereCustomerOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereQuantityMaximum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereQuantityMinimum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereRowOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereSalesPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereShareQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerOfferDetail whereSupplierOfferDetail($value)
 * @mixin Model
 */
class CustomerOfferDetail extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'customerofferdetail';
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'CustomerOffer',
        'Product',
        'ShareQuantity',
        'Currency',
        'QuantityUnit',
        'SalesPrice',
        'QuantityMinimum',
        'QuantityMaximum',
        'SupplierOfferDetail',
        'RowVersion',
        'RowOrder',
        'PriceCategory',
        'BasePrice',
        'BasePriceDate',
        'SalesPercent',
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
        'CustomerOffer' => 'integer',
        'Product' => 'integer',
        'ShareQuantity' => 'decimal:4',
        'Currency' => 'integer',
        'QuantityUnit' => 'integer',
        'SalesPrice' => 'decimal:4',
        'QuantityMinimum' => 'decimal:4',
        'QuantityMaximum' => 'decimal:4',
        'SupplierOfferDetail' => 'integer',
        'RowVersion' => 'datetime',
        'RowOrder' => 'integer',
        'PriceCategory' => 'integer',
        'BasePrice' => '',
        'BasePriceDate' => 'datetime',
        'SalesPercent' => '',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'CustomerOffer' => 'required',
        'Product' => 'required',
        'ShareQuantity' => 'nullable|numeric',
        'Currency' => 'required',
        'QuantityUnit' => 'nullable',
        'SalesPrice' => 'required|numeric',
        'QuantityMinimum' => 'nullable|numeric',
        'QuantityMaximum' => 'nullable|numeric',
        'SupplierOfferDetail' => 'nullable',
        'RowVersion' => 'required',
        'RowOrder' => 'required|integer'
    ];

    protected $append = ['customerOfferVoucherNumber', 'productName', 'customerOfferName',
                         'customerOfferValidFrom', 'customerOfferValidTo', 'currencyName',
                         'quantityUnitName', 'productPicture'];

    public function getCustomerOfferVoucherNumberAttribute()
    {
        return CustomerOffer::where('Id', $this->CustomerOffer)->first()->VoucherNumber;
    }

    public function getCustomerOfferNameAttribute()
    {
        return CustomerOffer::where('Id', $this->CustomerOffer)->first()->Name;
    }

    public function getCustomerOfferValidFromAttribute()
    {
        return CustomerOffer::where('Id', $this->CustomerOffer)->first()->ValidFrom;
    }

    public function getCustomerOfferValidToAttribute()
    {
        return CustomerOffer::where('Id', $this->CustomerOffer)->first()->ValidTo;
    }

    public function getProductNameAttribute()
    {
        return !empty($this->Product) ? Product::where('Id', $this->Product)->first()->Name : '';
    }

    public function getProductPictureAttribute()
    {
        return !empty($this->Product) ? Product::where('Id', $this->Product)->first()->Picture : '';
    }

    public function getCurrencyNameAttribute()
    {
        return Currency::where('Id', $this->Currency)->first()->Name;
    }

    public function getQuantityUnitNameAttribute()
    {
        return QuantityUnit::where('Id', $this->QuantityUnit)->first()->Name;
    }

}
