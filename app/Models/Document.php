<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Document
 *
 * @package App\Models
 * @version January 29, 2022, 9:32 am UTC
 * @property string $Name
 * @property string $Extension
 * @property string $CategoryName
 * @property string|\Carbon\Carbon $AttachDate
 * @property string $DetailInfo
 * @property string $Data
 * @property integer $Customer
 * @property integer $Product
 * @property integer $CustomerTouch
 * @property integer $CustomerOffer
 * @property integer $CustomerContract
 * @property integer $SupplierOffer
 * @property integer $SupplierContract
 * @property integer $Investment
 * @property integer $CustomerBid
 * @property integer $CustomerOrder
 * @property integer $InternalOrder
 * @property integer $StockOut
 * @property integer $SupplierBid
 * @property integer $SupplierOrder
 * @property integer $StockIn
 * @property integer $StockExchange
 * @property integer $Manufacturing
 * @property integer $MessageLog
 * @property integer $LeadId
 * @property integer $Opportunity
 * @property integer $Campaign
 * @property integer $Maintenance
 * @property integer $MaintenanceProduct
 * @property integer $Employee
 * @property integer $CheckoutVoucher
 * @property integer $Asset
 * @property integer $Vehicle
 * @property integer $Route
 * @property integer $Folder
 * @property integer $EmployeeDocuments
 * @property integer $EmployeeTaxBenefits
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property integer $FabricSchema
 * @property integer $Fabric
 * @property integer $FabricSchemaItem
 * @property integer $FabricItem
 * @property int $Id
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereAsset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereAttachDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCheckoutVoucher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCustomerBid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCustomerContract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCustomerOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCustomerOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCustomerTouch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDetailInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereEmployee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereEmployeeDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereEmployeeTaxBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereFabric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereFabricItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereFabricSchema($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereFabricSchemaItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereInternalOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereMaintenanceProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereManufacturing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereMessageLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereOpportunity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereStockExchange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereStockIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereStockOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereSupplierBid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereSupplierContract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereSupplierOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereSupplierOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereVehicle($value)
 * @mixin Model
 */
class Document extends Model
{
//    use SoftDeletes;

    use HasFactory;

    public $table = 'document';
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//
//
//    protected $dates = ['deleted_at'];



    public $fillable = [
        'Name',
        'Extension',
        'CategoryName',
        'AttachDate',
        'DetailInfo',
        'Data',
        'Customer',
        'Product',
        'CustomerTouch',
        'CustomerOffer',
        'CustomerContract',
        'SupplierOffer',
        'SupplierContract',
        'Investment',
        'CustomerBid',
        'CustomerOrder',
        'InternalOrder',
        'StockOut',
        'SupplierBid',
        'SupplierOrder',
        'StockIn',
        'StockExchange',
        'Manufacturing',
        'MessageLog',
        'LeadId',
        'Opportunity',
        'Campaign',
        'Maintenance',
        'MaintenanceProduct',
        'Employee',
        'CheckoutVoucher',
        'Asset',
        'Vehicle',
        'Route',
        'Folder',
        'EmployeeDocuments',
        'EmployeeTaxBenefits',
        'RowCreate',
        'RowModify',
        'FabricSchema',
        'Fabric',
        'FabricSchemaItem',
        'FabricItem'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Name' => 'string',
        'Extension' => 'string',
        'CategoryName' => 'string',
        'AttachDate' => 'datetime',
        'DetailInfo' => 'string',
        'Data' => 'string',
        'Customer' => 'integer',
        'Product' => 'integer',
        'CustomerTouch' => 'integer',
        'CustomerOffer' => 'integer',
        'CustomerContract' => 'integer',
        'SupplierOffer' => 'integer',
        'SupplierContract' => 'integer',
        'Investment' => 'integer',
        'CustomerBid' => 'integer',
        'CustomerOrder' => 'integer',
        'InternalOrder' => 'integer',
        'StockOut' => 'integer',
        'SupplierBid' => 'integer',
        'SupplierOrder' => 'integer',
        'StockIn' => 'integer',
        'StockExchange' => 'integer',
        'Manufacturing' => 'integer',
        'MessageLog' => 'integer',
        'LeadId' => 'integer',
        'Opportunity' => 'integer',
        'Campaign' => 'integer',
        'Maintenance' => 'integer',
        'MaintenanceProduct' => 'integer',
        'Employee' => 'integer',
        'CheckoutVoucher' => 'integer',
        'Asset' => 'integer',
        'Vehicle' => 'integer',
        'Route' => 'integer',
        'Folder' => 'integer',
        'EmployeeDocuments' => 'integer',
        'EmployeeTaxBenefits' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'FabricSchema' => 'integer',
        'Fabric' => 'integer',
        'FabricSchemaItem' => 'integer',
        'FabricItem' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Name' => 'required|string|max:100',
        'Extension' => 'required|string|max:100',
        'CategoryName' => 'nullable|string|max:100',
        'AttachDate' => 'nullable',
        'DetailInfo' => 'nullable|string|max:100',
        'Data' => 'nullable|string',
        'Customer' => 'nullable',
        'Product' => 'nullable',
        'CustomerTouch' => 'nullable',
        'CustomerOffer' => 'nullable',
        'CustomerContract' => 'nullable',
        'SupplierOffer' => 'nullable',
        'SupplierContract' => 'nullable',
        'Investment' => 'nullable',
        'CustomerBid' => 'nullable',
        'CustomerOrder' => 'nullable',
        'InternalOrder' => 'nullable',
        'StockOut' => 'nullable',
        'SupplierBid' => 'nullable',
        'SupplierOrder' => 'nullable',
        'StockIn' => 'nullable',
        'StockExchange' => 'nullable',
        'Manufacturing' => 'nullable',
        'MessageLog' => 'nullable',
        'LeadId' => 'nullable',
        'Opportunity' => 'nullable',
        'Campaign' => 'nullable',
        'Maintenance' => 'nullable',
        'MaintenanceProduct' => 'nullable',
        'Employee' => 'nullable',
        'CheckoutVoucher' => 'nullable',
        'Asset' => 'nullable',
        'Vehicle' => 'nullable',
        'Route' => 'nullable',
        'Folder' => 'nullable',
        'EmployeeDocuments' => 'nullable',
        'EmployeeTaxBenefits' => 'nullable',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'FabricSchema' => 'nullable',
        'Fabric' => 'nullable',
        'FabricSchemaItem' => 'nullable',
        'FabricItem' => 'nullable'
    ];


}
