<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerContactFavoriteProduct
 *
 * @package App\Models
 * @version May 24, 2022, 12:56 pm CEST
 * @property integer $customercontact_id
 * @property integer $product_id
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $customer_contact_name
 * @property-read mixed $product_name
 * @method static \Database\Factories\CustomerContactFavoriteProductFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|CustomerContactFavoriteProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct whereCustomercontactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContactFavoriteProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CustomerContactFavoriteProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CustomerContactFavoriteProduct withoutTrashed()
 * @mixin Model
 */
class CustomerContactFavoriteProduct extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'customercontactfavoriteproduct';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'customercontact_id',
        'product_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customercontact_id' => 'integer',
        'product_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'customercontact_id' => 'required|integer',
        'product_id' => 'required|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    protected $append = [
        'ProductName',
        'CustomerContactName'
    ];

    public function getProductNameAttribute() {
        $product = Product::where('Id', $this->product_id)->first();
        return !empty($this->product_id) ? !empty($product) ? $product->Name : ' ' : ' ';
    }

    public function getCustomerContactNameAttribute() {
        $customerContact = CustomerContact::where('Id', $this->customercontact_id)->first();
        return !empty($this->customercontact_id) ? !empty($customerContact) ? $customerContact->Name : ' ' : ' ';
    }

    public function productRelation() {
        return $this->belongsTo(Product::class, 'product_id', 'Id');
    }

    public function productNameRelation() {
        return $this->belongsTo(Product::class, 'product_id', 'Id')->select(['Id', 'Name']);
    }

    public function customercontactRelation() {
        return $this->belongsTo(CustomerContact::class, 'customercontact_id', 'Id');
    }

}
