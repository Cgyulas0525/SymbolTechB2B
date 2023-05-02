<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Warehouse
 *
 * @package App\Models
 * @version January 19, 2022, 9:54 am UTC
 * @property integer $Site
 * @property string $Name
 * @property integer $AllowNegativeBalance
 * @property integer $PermissionProtected
 * @property integer $Trust
 * @property integer $TrustCustomer
 * @property integer $TrustCustomerAddress
 * @property integer $OwnerEmployee
 * @property integer $OwnerInvestment
 * @property integer $SellBanned
 * @property integer $Foreignn
 * @property string $Zip
 * @property string $City
 * @property string $Street
 * @property string $HouseNumber
 * @property string $ContactName
 * @property string $Phone
 * @property string $Fax
 * @property string $Email
 * @property string $Comment
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property string $GLN
 * @property integer $IsConsigner
 * @property int $Id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomerBid[] $CustomerBid
 * @property-read int|null $customer_bid_count
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereAllowNegativeBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereForeignn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereGLN($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereIsConsigner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereOwnerEmployee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereOwnerInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePermissionProtected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereSellBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereTrust($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereTrustCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereTrustCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereZip($value)
 * @mixin Model
 */
class Warehouse extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'warehouse';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Site',
        'Name',
        'AllowNegativeBalance',
        'PermissionProtected',
        'Trust',
        'TrustCustomer',
        'TrustCustomerAddress',
        'OwnerEmployee',
        'OwnerInvestment',
        'SellBanned',
        'Foreignn',
        'Zip',
        'City',
        'Street',
        'HouseNumber',
        'ContactName',
        'Phone',
        'Fax',
        'Email',
        'Comment',
        'Deleted',
        'RowCreate',
        'RowModify',
        'GLN',
        'IsConsigner'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Site' => 'integer',
        'Name' => 'string',
        'AllowNegativeBalance' => 'integer',
        'PermissionProtected' => 'integer',
        'Trust' => 'integer',
        'TrustCustomer' => 'integer',
        'TrustCustomerAddress' => 'integer',
        'OwnerEmployee' => 'integer',
        'OwnerInvestment' => 'integer',
        'SellBanned' => 'integer',
        'Foreignn' => 'integer',
        'Zip' => 'string',
        'City' => 'string',
        'Street' => 'string',
        'HouseNumber' => 'string',
        'ContactName' => 'string',
        'Phone' => 'string',
        'Fax' => 'string',
        'Email' => 'string',
        'Comment' => 'string',
        'Deleted' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'GLN' => 'string',
        'IsConsigner' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Site' => 'required',
        'Name' => 'required|string|max:100',
        'AllowNegativeBalance' => 'required',
        'PermissionProtected' => 'required',
        'Trust' => 'required',
        'TrustCustomer' => 'nullable',
        'TrustCustomerAddress' => 'nullable',
        'OwnerEmployee' => 'nullable',
        'OwnerInvestment' => 'nullable',
        'SellBanned' => 'required',
        'Foreignn' => 'required',
        'Zip' => 'nullable|string|max:10',
        'City' => 'nullable|string|max:100',
        'Street' => 'nullable|string|max:100',
        'HouseNumber' => 'nullable|string|max:20',
        'ContactName' => 'nullable|string|max:100',
        'Phone' => 'nullable|string|max:20',
        'Fax' => 'nullable|string|max:20',
        'Email' => 'nullable|string|max:100',
        'Comment' => 'nullable|string|max:65535',
        'Deleted' => 'required',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'GLN' => 'nullable|string|max:40',
        'IsConsigner' => 'required'
    ];

    public function CustomerBidRelation() {
        return $this->hasMany(CustomerBid::class, 'Warehouse', 'id');
    }

    public function warehouseBalanceRelation() {
        return $this->hasMany(WarehouseBalance::class, 'Warehouse', 'id');
    }

    public function warehouseDailyBalanceRelation() {
        return $this->hasMany(WarehouseDailyBalance::class, 'Warehouse', 'id');
    }

}
