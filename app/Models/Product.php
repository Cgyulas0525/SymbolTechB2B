<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Product
 *
 * @package App\Models
 * @version January 19, 2022, 9:47 am UTC
 * @property string $Code
 * @property integer $CodeHidden
 * @property string $Barcode
 * @property string $Name
 * @property integer $Inactive
 * @property string|\Carbon\Carbon $CreateDateTime
 * @property integer $PrimeSupplier
 * @property integer $Manufacturer
 * @property integer $ProductCategoryExport
 * @property integer $Vat
 * @property integer $VatBuy
 * @property integer $SellBanned
 * @property integer $BuyBanned
 * @property integer $RunOut
 * @property integer $Service
 * @property integer $MediateService
 * @property integer $ZeroPriceAllowed
 * @property integer $Accumulator
 * @property integer $AccProduct
 * @property integer $VisibleInPriceList
 * @property integer $QuantityUnit
 * @property integer $QuantityDigits
 * @property integer $PriceDigits
 * @property string $PriceDigitsExt
 * @property integer $GrossPrices
 * @property integer $SupplierPriceAffected
 * @property integer $SupplierPriceTolerance
 * @property integer $SupplierInPriceOnly
 * @property integer $SupplierToSysCurrency
 * @property integer $SupplierToBaseQU
 * @property integer $WeightControll
 * @property integer $AttachmentRoll
 * @property string $CustomsTariffNumber
 * @property number $Weight
 * @property number $DimensionWidth
 * @property number $DimensionHeight
 * @property number $DimensionDepth
 * @property number $QuantityMin
 * @property number $QuantityMax
 * @property number $QuantityOpt
 * @property number $QtyPackage
 * @property number $QtyLevel
 * @property number $QtyPallet
 * @property integer $IstatKN
 * @property integer $IstatCountryOrigin
 * @property number $IncidentExpense
 * @property number $IncidentExpensePerc
 * @property integer $GuaranteeMonths
 * @property integer $GuaranteeMode
 * @property number $GuaranteeMinUnitPrice
 * @property integer $BestBeforeValue
 * @property integer $BestBeforeIsDay
 * @property string $PriceCategoryRule
 * @property integer $MustMunufacturing
 * @property integer $StrictManufacturing
 * @property integer $SerialMode
 * @property string $SerialSetting
 * @property integer $ShelfMode
 * @property integer $ClearAllocation
 * @property string $DefaultAlias
 * @property number $DepositPercent
 * @property string $Pictogram
 * @property string $Comment
 * @property string $WebName
 * @property string $WebDescription
 * @property string $WebUrl
 * @property string $Picture
 * @property string $StrExA
 * @property string $StrExB
 * @property string $StrExC
 * @property string $StrExD
 * @property string|\Carbon\Carbon $DateExA
 * @property string|\Carbon\Carbon $DateExB
 * @property number $NumExA
 * @property number $NumExB
 * @property number $NumExC
 * @property integer $BoolExA
 * @property integer $BoolExB
 * @property integer $LookupExA
 * @property integer $LookupExB
 * @property integer $LookupExC
 * @property integer $LookupExD
 * @property integer $Deleted
 * @property string|\Carbon\Carbon $RowVersion
 * @property number $MinProfitPercent
 * @property number $ManufacturingCost
 * @property integer $SerialAutoMaintenance
 * @property integer $AdrMaterial
 * @property integer $AdrPackage
 * @property number $WeightNet
 * @property string $MemoExA
 * @property string $MemoExB
 * @property string|\Carbon\Carbon $DateExC
 * @property string|\Carbon\Carbon $DateExD
 * @property number $NumExD
 * @property integer $BoolExC
 * @property integer $BoolExD
 * @property string $MemoExC
 * @property string $MemoExD
 * @property string $WebMetaDescription
 * @property string $WebKeywords
 * @property integer $WebDisplay
 * @property integer $LookupExE
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property number $FillingVolume
 * @property integer $PublicHealthPT
 * @property string $VoucherRules
 * @property integer $IsLarge
 * @property integer $UseWarrantyRule
 * @property integer $AdrCalcBasis
 * @property integer $EuVat
 * @property integer $EuVatBuy
 * @property integer $NonEuVat
 * @property integer $NonEuVatBuy
 * @property integer $BidAllowed
 * @property integer $IsPallet
 * @property integer $IsFragile
 * @property int $Id
 * @property int|null $ProductCategory
 * @property \Illuminate\Support\Carbon|null $PictureDateTime
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAccProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAccumulator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAdrCalcBasis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAdrMaterial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAdrPackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAttachmentRoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBestBeforeIsDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBestBeforeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBidAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBoolExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBoolExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBoolExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBoolExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBuyBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereClearAllocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCodeHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreateDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCustomsTariffNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDateExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDateExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDateExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDateExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDefaultAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDepositPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDimensionDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDimensionHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDimensionWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEuVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEuVatBuy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereFillingVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereGrossPrices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereGuaranteeMinUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereGuaranteeMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereGuaranteeMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereInactive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIncidentExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIncidentExpensePerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsFragile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsLarge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsPallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIstatCountryOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIstatKN($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLookupExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLookupExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLookupExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLookupExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereLookupExE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereManufacturingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMediateService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMemoExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMemoExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMemoExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMemoExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinProfitPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMustMunufacturing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNonEuVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNonEuVatBuy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNumExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNumExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNumExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNumExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePictogram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePictureDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePriceCategoryRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePriceDigits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePriceDigitsExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrimeSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePublicHealthPT($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQtyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQtyPackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQtyPallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityDigits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityOpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRunOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSellBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSerialAutoMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSerialMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSerialSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShelfMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStrExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStrExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStrExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStrExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStrictManufacturing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSupplierInPriceOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSupplierPriceAffected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSupplierPriceTolerance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSupplierToBaseQU($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSupplierToSysCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUseWarrantyRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVatBuy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVisibleInPriceList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVoucherRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWebDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWebDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWebKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWebMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWebName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWebUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWeightControll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWeightNet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereZeroPriceAllowed($value)
 * @mixin Model
 */
class Product extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'product';
    public $timestamps = false;

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';


    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Code',
        'CodeHidden',
        'Barcode',
        'Name',
        'Inactive',
        'CreateDateTime',
        'PrimeSupplier',
        'Manufacturer',
        'ProductCategory',
        'Vat',
        'VatBuy',
        'SellBanned',
        'BuyBanned',
        'RunOut',
        'Service',
        'MediateService',
        'ZeroPriceAllowed',
        'Accumulator',
        'AccProduct',
        'VisibleInPriceList',
        'QuantityUnit',
        'QuantityDigits',
        'PriceDigits',
        'PriceDigitsExt',
        'GrossPrices',
        'SupplierPriceAffected',
        'SupplierPriceTolerance',
        'SupplierInPriceOnly',
        'SupplierToSysCurrency',
        'SupplierToBaseQU',
        'WeightControll',
        'AttachmentRoll',
        'CustomsTariffNumber',
        'Weight',
        'DimensionWidth',
        'DimensionHeight',
        'DimensionDepth',
        'QuantityMin',
        'QuantityMax',
        'QuantityOpt',
        'QtyPackage',
        'QtyLevel',
        'QtyPallet',
        'IstatKN',
        'IstatCountryOrigin',
        'IncidentExpense',
        'IncidentExpensePerc',
        'GuaranteeMonths',
        'GuaranteeMode',
        'GuaranteeMinUnitPrice',
        'BestBeforeValue',
        'BestBeforeIsDay',
        'PriceCategoryRule',
        'MustMunufacturing',
        'StrictManufacturing',
        'SerialMode',
        'SerialSetting',
        'ShelfMode',
        'ClearAllocation',
        'DefaultAlias',
        'DepositPercent',
        'Pictogram',
        'Comment',
        'WebName',
        'WebDescription',
        'WebUrl',
        'Picture',
        'StrExA',
        'StrExB',
        'StrExC',
        'StrExD',
        'DateExA',
        'DateExB',
        'NumExA',
        'NumExB',
        'NumExC',
        'BoolExA',
        'BoolExB',
        'LookupExA',
        'LookupExB',
        'LookupExC',
        'LookupExD',
        'Deleted',
        'RowVersion',
        'MinProfitPercent',
        'ManufacturingCost',
        'SerialAutoMaintenance',
        'AdrMaterial',
        'AdrPackage',
        'WeightNet',
        'MemoExA',
        'MemoExB',
        'DateExC',
        'DateExD',
        'NumExD',
        'BoolExC',
        'BoolExD',
        'MemoExC',
        'MemoExD',
        'WebMetaDescription',
        'WebKeywords',
        'WebDisplay',
        'LookupExE',
        'RowCreate',
        'RowModify',
        'FillingVolume',
        'PublicHealthPT',
        'VoucherRules',
        'IsLarge',
        'UseWarrantyRule',
        'AdrCalcBasis',
        'EuVat',
        'EuVatBuy',
        'NonEuVat',
        'NonEuVatBuy',
        'BidAllowed',
        'IsPallet',
        'IsFragile',
        'PictureDateTime',
        'MinSellQuantity',
        'StrExE',
        'StrExF',
        'StrExG',
        'StrExH',
        'StrExI',
        'StrExJ',
        'DateExE',
        'DateExF',
        'DateExG',
        'DateExH',
        'DateExI',
        'DateExJ',
        'NumExE',
        'NumExF',
        'NumExG',
        'NumExH',
        'NumExI',
        'NumExJ',
        'BoolExE',
        'BoolExF',
        'BoolExG',
        'BoolExH',
        'BoolExI',
        'BoolExJ',
        'LookupExF',
        'LookupExG',
        'LookupExH',
        'LookupExI',
        'LookupExJ',
        'MemoExE',
        'MemoExF',
        'MemoExG',
        'MemoExH',
        'MemoExI',
        'MemoExJ',
        'MinSellPrice',
        'MinSellBelowPrice'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Code' => 'string',
        'CodeHidden' => 'integer',
        'Barcode' => 'string',
        'Name' => 'string',
        'Inactive' => 'integer',
        'CreateDateTime' => 'datetime',
        'PrimeSupplier' => 'integer',
        'Manufacturer' => 'integer',
        'ProductCategory' => 'integer',
        'Vat' => 'integer',
        'VatBuy' => 'integer',
        'SellBanned' => 'integer',
        'BuyBanned' => 'integer',
        'RunOut' => 'integer',
        'Service' => 'integer',
        'MediateService' => 'integer',
        'ZeroPriceAllowed' => 'integer',
        'Accumulator' => 'integer',
        'AccProduct' => 'integer',
        'VisibleInPriceList' => 'integer',
        'QuantityUnit' => 'integer',
        'QuantityDigits' => 'integer',
        'PriceDigits' => 'integer',
        'PriceDigitsExt' => 'string',
        'GrossPrices' => 'integer',
        'SupplierPriceAffected' => 'integer',
        'SupplierPriceTolerance' => 'integer',
        'SupplierInPriceOnly' => 'integer',
        'SupplierToSysCurrency' => 'integer',
        'SupplierToBaseQU' => 'integer',
        'WeightControll' => 'integer',
        'AttachmentRoll' => 'integer',
        'CustomsTariffNumber' => 'string',
        'Weight' => 'decimal:4',
        'DimensionWidth' => 'decimal:4',
        'DimensionHeight' => 'decimal:4',
        'DimensionDepth' => 'decimal:4',
        'QuantityMin' => 'decimal:4',
        'QuantityMax' => 'decimal:4',
        'QuantityOpt' => 'decimal:4',
        'QtyPackage' => 'decimal:4',
        'QtyLevel' => 'decimal:4',
        'QtyPallet' => 'decimal:4',
        'IstatKN' => 'integer',
        'IstatCountryOrigin' => 'integer',
        'IncidentExpense' => 'decimal:4',
        'IncidentExpensePerc' => 'decimal:4',
        'GuaranteeMonths' => 'integer',
        'GuaranteeMode' => 'integer',
        'GuaranteeMinUnitPrice' => 'decimal:4',
        'BestBeforeValue' => 'integer',
        'BestBeforeIsDay' => 'integer',
        'PriceCategoryRule' => 'string',
        'MustMunufacturing' => 'integer',
        'StrictManufacturing' => 'integer',
        'SerialMode' => 'integer',
        'SerialSetting' => 'string',
        'ShelfMode' => 'integer',
        'ClearAllocation' => 'integer',
        'DefaultAlias' => 'string',
        'DepositPercent' => 'decimal:4',
        'Pictogram' => 'string',
        'Comment' => 'string',
        'WebName' => 'string',
        'WebDescription' => 'string',
        'WebUrl' => 'string',
        'Picture' => 'string',
        'StrExA' => 'string',
        'StrExB' => 'string',
        'StrExC' => 'string',
        'StrExD' => 'string',
        'DateExA' => 'datetime',
        'DateExB' => 'datetime',
        'NumExA' => 'decimal:4',
        'NumExB' => 'decimal:4',
        'NumExC' => 'decimal:4',
        'BoolExA' => 'integer',
        'BoolExB' => 'integer',
        'LookupExA' => 'integer',
        'LookupExB' => 'integer',
        'LookupExC' => 'integer',
        'LookupExD' => 'integer',
        'Deleted' => 'integer',
        'RowVersion' => 'datetime',
        'MinProfitPercent' => 'decimal:4',
        'ManufacturingCost' => 'decimal:4',
        'SerialAutoMaintenance' => 'integer',
        'AdrMaterial' => 'integer',
        'AdrPackage' => 'integer',
        'WeightNet' => 'decimal:4',
        'MemoExA' => 'string',
        'MemoExB' => 'string',
        'DateExC' => 'datetime',
        'DateExD' => 'datetime',
        'NumExD' => 'decimal:4',
        'BoolExC' => 'integer',
        'BoolExD' => 'integer',
        'MemoExC' => 'string',
        'MemoExD' => 'string',
        'WebMetaDescription' => 'string',
        'WebKeywords' => 'string',
        'WebDisplay' => 'integer',
        'LookupExE' => 'integer',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'FillingVolume' => 'decimal:4',
        'PublicHealthPT' => 'integer',
        'VoucherRules' => 'string',
        'IsLarge' => 'integer',
        'UseWarrantyRule' => 'integer',
        'AdrCalcBasis' => 'integer',
        'EuVat' => 'integer',
        'EuVatBuy' => 'integer',
        'NonEuVat' => 'integer',
        'NonEuVatBuy' => 'integer',
        'BidAllowed' => 'integer',
        'IsPallet' => 'integer',
        'IsFragile' => 'integer',
        'PictureDateTime' => 'datetime',
        'MinSellQuantity' => 'decimal:4',
        'StrExE' => 'string',
        'StrExF' => 'string',
        'StrExG' => 'string',
        'StrExH' => 'string',
        'StrExI' => 'string',
        'StrExJ' => 'string',
        'DateExE' => 'datetime',
        'DateExF' => 'datetime',
        'DateExG' => 'datetime',
        'DateExH' => 'datetime',
        'DateExI' => 'datetime',
        'DateExJ' => 'datetime',
        'NumExE' => '',
        'NumExF' => '',
        'NumExG' => '',
        'NumExH' => '',
        'NumExI' => '',
        'NumExJ' => '',
        'BoolExE' => 'integer',
        'BoolExF' => 'integer',
        'BoolExG' => 'integer',
        'BoolExH' => 'integer',
        'BoolExI' => 'integer',
        'BoolExJ' => 'integer',
        'LookupExF' => 'integer',
        'LookupExG' => 'integer',
        'LookupExH' => 'integer',
        'LookupExI' => 'integer',
        'LookupExJ' => 'integer',
        'MemoExE' => 'string',
        'MemoExF' => 'string',
        'MemoExG' => 'string',
        'MemoExH' => 'string',
        'MemoExI' => 'string',
        'MemoExJ' => 'string',
        'MinSellPrice' => 'decimal:4',
        'MinSellBelowPrice' => 'decimal:4'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Code' => 'required|string|max:40',
        'CodeHidden' => 'required',
        'Barcode' => 'nullable|string|max:100',
        'Name' => 'required|string|max:100',
        'Inactive' => 'required',
        'CreateDateTime' => 'nullable',
        'PrimeSupplier' => 'nullable',
        'Manufacturer' => 'nullable',
        'ProductCategoryExport' => 'nullable',
        'Vat' => 'nullable',
        'VatBuy' => 'nullable',
        'SellBanned' => 'required',
        'BuyBanned' => 'required',
        'RunOut' => 'required',
        'Service' => 'required',
        'MediateService' => 'required',
        'ZeroPriceAllowed' => 'required',
        'Accumulator' => 'required',
        'AccProduct' => 'nullable',
        'VisibleInPriceList' => 'required',
        'QuantityUnit' => 'nullable',
        'QuantityDigits' => 'required|integer',
        'PriceDigits' => 'required|integer',
        'PriceDigitsExt' => 'nullable|string|max:100',
        'GrossPrices' => 'required',
        'SupplierPriceAffected' => 'required',
        'SupplierPriceTolerance' => 'required|integer',
        'SupplierInPriceOnly' => 'required',
        'SupplierToSysCurrency' => 'nullable',
        'SupplierToBaseQU' => 'required',
        'WeightControll' => 'required',
        'AttachmentRoll' => 'required',
        'CustomsTariffNumber' => 'nullable|string|max:100',
        'Weight' => 'nullable|numeric',
        'DimensionWidth' => 'nullable|numeric',
        'DimensionHeight' => 'nullable|numeric',
        'DimensionDepth' => 'nullable|numeric',
        'QuantityMin' => 'nullable|numeric',
        'QuantityMax' => 'nullable|numeric',
        'QuantityOpt' => 'nullable|numeric',
        'QtyPackage' => 'nullable|numeric',
        'QtyLevel' => 'nullable|numeric',
        'QtyPallet' => 'nullable|numeric',
        'IstatKN' => 'nullable',
        'IstatCountryOrigin' => 'nullable',
        'IncidentExpense' => 'nullable|numeric',
        'IncidentExpensePerc' => 'nullable|numeric',
        'GuaranteeMonths' => 'nullable|integer',
        'GuaranteeMode' => 'nullable',
        'GuaranteeMinUnitPrice' => 'nullable|numeric',
        'BestBeforeValue' => 'nullable|integer',
        'BestBeforeIsDay' => 'required',
        'PriceCategoryRule' => 'nullable|string|max:65535',
        'MustMunufacturing' => 'required',
        'StrictManufacturing' => 'required',
        'SerialMode' => 'required|integer',
        'SerialSetting' => 'nullable|string|max:65535',
        'ShelfMode' => 'required|integer',
        'ClearAllocation' => 'required',
        'DefaultAlias' => 'nullable|string|max:100',
        'DepositPercent' => 'nullable|numeric',
        'Pictogram' => 'nullable|string|max:100',
        'Comment' => 'nullable|string|max:65535',
        'WebName' => 'nullable|string|max:100',
        'WebDescription' => 'nullable|string|max:65535',
        'WebUrl' => 'nullable|string|max:100',
        'Picture' => 'nullable|string|max:65535',
        'StrExA' => 'nullable|string|max:100',
        'StrExB' => 'nullable|string|max:100',
        'StrExC' => 'nullable|string|max:100',
        'StrExD' => 'nullable|string|max:100',
        'DateExA' => 'nullable',
        'DateExB' => 'nullable',
        'NumExA' => 'nullable|numeric',
        'NumExB' => 'nullable|numeric',
        'NumExC' => 'nullable|numeric',
        'BoolExA' => 'required',
        'BoolExB' => 'required',
        'LookupExA' => 'nullable',
        'LookupExB' => 'nullable',
        'LookupExC' => 'nullable',
        'LookupExD' => 'nullable',
        'Deleted' => 'required',
        'RowVersion' => 'nullable',
        'MinProfitPercent' => 'nullable|numeric',
        'ManufacturingCost' => 'nullable|numeric',
        'SerialAutoMaintenance' => 'required',
        'AdrMaterial' => 'nullable',
        'AdrPackage' => 'nullable',
        'WeightNet' => 'nullable|numeric',
        'MemoExA' => 'nullable|string|max:65535',
        'MemoExB' => 'nullable|string|max:65535',
        'DateExC' => 'nullable',
        'DateExD' => 'nullable',
        'NumExD' => 'nullable|numeric',
        'BoolExC' => 'required',
        'BoolExD' => 'required',
        'MemoExC' => 'nullable|string|max:65535',
        'MemoExD' => 'nullable|string|max:65535',
        'WebMetaDescription' => 'nullable|string|max:65535',
        'WebKeywords' => 'nullable|string|max:100',
        'WebDisplay' => 'required',
        'LookupExE' => 'nullable',
        'RowCreate' => 'nullable',
        'RowModify' => 'nullable',
        'FillingVolume' => 'nullable|numeric',
        'PublicHealthPT' => 'nullable',
        'VoucherRules' => 'nullable|string|max:65535',
        'IsLarge' => 'required',
        'UseWarrantyRule' => 'required',
        'AdrCalcBasis' => 'required|integer',
        'EuVat' => 'nullable',
        'EuVatBuy' => 'nullable',
        'NonEuVat' => 'nullable',
        'NonEuVatBuy' => 'nullable',
        'BidAllowed' => 'required',
        'IsPallet' => 'required',
        'IsFragile' => 'required'
    ];

    public function customercontactfavoriteproduct() {
        return $this->hasMany(CustomerContactFavoriteProduct::class, 'product_id', 'Id');
    }


}
