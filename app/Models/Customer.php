<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Customer
 *
 * @package App\Models
 * @version January 19, 2022, 9:44 am UTC
 * @property string $Code
 * @property integer $CustomerStatus
 * @property integer $SupplierStatus
 * @property string $Name
 * @property string $SearchName
 * @property string|\Carbon\Carbon $CreateDateTime
 * @property integer $CustomerCategory
 * @property integer $SupplierCategory
 * @property integer $DisplayCountry
 * @property string $InvoiceCountry
 * @property string $InvoiceRegion
 * @property string $InvoiceZip
 * @property string $InvoiceCity
 * @property string $InvoiceStreet
 * @property string $InvoiceHouseNumber
 * @property integer $MailBanned
 * @property string $MailCountry
 * @property string $MailRegion
 * @property string $MailName
 * @property integer $MailOriginalName
 * @property string $MailZip
 * @property string $MailCity
 * @property string $MailStreet
 * @property string $MailHouseNumber
 * @property integer $PaymentMethod
 * @property integer $PaymentMethodStrict
 * @property integer $PaymentMethodToleranceDay
 * @property integer $PriceCategory
 * @property integer $CustomerIstatTemplate
 * @property integer $SupplierIstatTemplate
 * @property integer $Currency
 * @property integer $TransportMode
 * @property string $TradeRegNumber
 * @property string $TaxNumber
 * @property string $EUTaxNumber
 * @property string $GroupTaxNUmber
 * @property integer $EUMembership
 * @property string $BankAccount
 * @property string $BankAccountIBAN
 * @property string $ContactName
 * @property string $Phone
 * @property string $Fax
 * @property string $Sms
 * @property string $Email
 * @property integer $RobinsonMode
 * @property integer $AllowEmailVouchers
 * @property integer $SpecVoucherEmails
 * @property string $WebUsername
 * @property string $WebPassword
 * @property string $DeliveryInfo
 * @property string $Comment
 * @property string $VoucherRules
 * @property number $DiscountPercent
 * @property string $DebitQuota
 * @property string $EInvoice
 * @property string $BuyCompanyCode
 * @property string $SellCompanyCode
 * @property integer $Agent
 * @property integer $AgentStrict
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
 * @property integer $DeliveryCDay
 * @property integer $DeliverySDay
 * @property integer $SelfSupplierInvoice
 * @property string $Url
 * @property string $MemoExA
 * @property string $MemoExB
 * @property string|\Carbon\Carbon $DateExC
 * @property string|\Carbon\Carbon $DateExD
 * @property number $NumExD
 * @property integer $BoolExC
 * @property integer $BoolExD
 * @property string $MemoExC
 * @property string $MemoExD
 * @property string $SupplierDebitQuota
 * @property integer $DebitQIgnoreOnce
 * @property string $BankName
 * @property string $BankSwiftCode
 * @property number $SupplierDiscountPercent
 * @property string|\Carbon\Carbon $RowCreate
 * @property string|\Carbon\Carbon $RowModify
 * @property integer $IsCompany
 * @property string $InvoiceTownship
 * @property string $MailTownship
 * @property string $GLN
 * @property integer $PaymentMethodLimitSkip
 * @property integer $SupplierPaymentMethod
 * @property integer $SupplierPMStrict
 * @property integer $SupplierPMToleranceDay
 * @property string $NAVOnlineInvoiceUsername
 * @property string $NAVOnlineInvoicePassword
 * @property string $NAVOnlineInvoiceSignature
 * @property string $NAVOnlineInvoiceDecode
 * @property integer $NAVOnlineInvoiceInactive
 * @property integer $InvoiceCustomer
 * @property number $BuyLimit
 * @property integer $ParcelInfo
 * @property int $Id
 * @property \Illuminate\Support\Carbon|null $DiscountPercentDateTime
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAgentStrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAllowEmailVouchers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBankAccountIBAN($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBankSwiftCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBoolExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBoolExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBoolExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBoolExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBuyCompanyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereBuyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreateDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerIstatTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDateExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDateExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDateExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDateExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDebitQIgnoreOnce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDebitQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeliveryCDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeliveryInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeliverySDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDiscountPercentDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDisplayCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEUMembership($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEUTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereGLN($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereGroupTaxNUmber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceCustomer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceTownship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereInvoiceZip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereIsCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLookupExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLookupExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLookupExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereLookupExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailTownship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMailZip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMemoExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMemoExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMemoExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMemoExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNAVOnlineInvoiceDecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNAVOnlineInvoiceInactive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNAVOnlineInvoicePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNAVOnlineInvoiceSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNAVOnlineInvoiceUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNumExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNumExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNumExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNumExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereParcelInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePaymentMethodLimitSkip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePaymentMethodStrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePaymentMethodToleranceDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePriceCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRobinsonMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRowCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRowModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRowVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSearchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSelfSupplierInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSellCompanyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSpecVoucherEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStrExA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStrExB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStrExC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStrExD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierDebitQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierIstatTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierPMStrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierPMToleranceDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSupplierStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereTradeRegNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereTransportMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereVoucherRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereWebPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereWebUsername($value)
 * @mixin Model
 */
class Customer extends Model
{
//    // use SoftDeletes;

    use HasFactory;

    public $table = 'customer';
    public $timestamps = false;

//    // const CREATED_AT = 'created_at';
//    // const UPDATED_AT = 'updated_at';
//
//
//    // protected $dates = ['deleted_at'];



    public $fillable = [
        'Id',
        'Code',
        'CustomerStatus',
        'SupplierStatus',
        'Name',
        'SearchName',
        'CreateDateTime',
        'CustomerCategory',
        'SupplierCategory',
        'DisplayCountry',
        'InvoiceCountry',
        'InvoiceRegion',
        'InvoiceZip',
        'InvoiceCity',
        'InvoiceStreet',
        'InvoiceHouseNumber',
        'MailBanned',
        'MailCountry',
        'MailRegion',
        'MailName',
        'MailOriginalName',
        'MailZip',
        'MailCity',
        'MailStreet',
        'MailHouseNumber',
        'PaymentMethod',
        'PaymentMethodStrict',
        'PaymentMethodToleranceDay',
        'PriceCategory',
        'CustomerIstatTemplate',
        'SupplierIstatTemplate',
        'Currency',
        'TransportMode',
        'TradeRegNumber',
        'TaxNumber',
        'EUTaxNumber',
        'GroupTaxNUmber',
        'EUMembership',
        'BankAccount',
        'BankAccountIBAN',
        'ContactName',
        'Phone',
        'Fax',
        'Sms',
        'Email',
        'RobinsonMode',
        'AllowEmailVouchers',
        'SpecVoucherEmails',
        'WebUsername',
        'WebPassword',
        'DeliveryInfo',
        'Comment',
        'VoucherRules',
        'DiscountPercent',
        'DebitQuota',
        'EInvoice',
        'BuyCompanyCode',
        'SellCompanyCode',
        'Agent',
        'AgentStrict',
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
        'DeliveryCDay',
        'DeliverySDay',
        'SelfSupplierInvoice',
        'Url',
        'MemoExA',
        'MemoExB',
        'DateExC',
        'DateExD',
        'NumExD',
        'BoolExC',
        'BoolExD',
        'MemoExC',
        'MemoExD',
        'SupplierDebitQuota',
        'DebitQIgnoreOnce',
        'BankName',
        'BankSwiftCode',
        'SupplierDiscountPercent',
        'RowCreate',
        'RowModify',
        'IsCompany',
        'InvoiceTownship',
        'MailTownship',
        'GLN',
        'PaymentMethodLimitSkip',
        'SupplierPaymentMethod',
        'SupplierPMStrict',
        'SupplierPMToleranceDay',
        'NAVOnlineInvoiceUsername',
        'NAVOnlineInvoicePassword',
        'NAVOnlineInvoiceSignature',
        'NAVOnlineInvoiceDecode',
        'NAVOnlineInvoiceInactive',
        'InvoiceCustomer',
        'BuyLimit',
        'ParcelInfo',
        'DiscountPercentDateTime',
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
        'LookupExE',
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
        'CompanyType'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Id' => 'integer',
        'Code' => 'string',
        'CustomerStatus' => 'integer',
        'SupplierStatus' => 'integer',
        'Name' => 'string',
        'SearchName' => 'string',
        'CreateDateTime' => 'datetime',
        'CustomerCategory' => 'integer',
        'SupplierCategory' => 'integer',
        'DisplayCountry' => 'integer',
        'InvoiceCountry' => 'string',
        'InvoiceRegion' => 'string',
        'InvoiceZip' => 'string',
        'InvoiceCity' => 'string',
        'InvoiceStreet' => 'string',
        'InvoiceHouseNumber' => 'string',
        'MailBanned' => 'integer',
        'MailCountry' => 'string',
        'MailRegion' => 'string',
        'MailName' => 'string',
        'MailOriginalName' => 'integer',
        'MailZip' => 'string',
        'MailCity' => 'string',
        'MailStreet' => 'string',
        'MailHouseNumber' => 'string',
        'PaymentMethod' => 'integer',
        'PaymentMethodStrict' => 'integer',
        'PaymentMethodToleranceDay' => 'integer',
        'PriceCategory' => 'integer',
        'CustomerIstatTemplate' => 'integer',
        'SupplierIstatTemplate' => 'integer',
        'Currency' => 'integer',
        'TransportMode' => 'integer',
        'TradeRegNumber' => 'string',
        'TaxNumber' => 'string',
        'EUTaxNumber' => 'string',
        'GroupTaxNUmber' => 'string',
        'EUMembership' => 'integer',
        'BankAccount' => 'string',
        'BankAccountIBAN' => 'string',
        'ContactName' => 'string',
        'Phone' => 'string',
        'Fax' => 'string',
        'Sms' => 'string',
        'Email' => 'string',
        'RobinsonMode' => 'integer',
        'AllowEmailVouchers' => 'integer',
        'SpecVoucherEmails' => 'integer',
        'WebUsername' => 'string',
        'WebPassword' => 'string',
        'DeliveryInfo' => 'string',
        'Comment' => 'string',
        'VoucherRules' => 'string',
        'DiscountPercent' => 'decimal:4',
        'DebitQuota' => 'string',
        'EInvoice' => 'string',
        'BuyCompanyCode' => 'string',
        'SellCompanyCode' => 'string',
        'Agent' => 'integer',
        'AgentStrict' => 'integer',
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
        'DeliveryCDay' => 'integer',
        'DeliverySDay' => 'integer',
        'SelfSupplierInvoice' => 'integer',
        'Url' => 'string',
        'MemoExA' => 'string',
        'MemoExB' => 'string',
        'DateExC' => 'datetime',
        'DateExD' => 'datetime',
        'NumExD' => 'decimal:4',
        'BoolExC' => 'integer',
        'BoolExD' => 'integer',
        'MemoExC' => 'string',
        'MemoExD' => 'string',
        'SupplierDebitQuota' => 'string',
        'DebitQIgnoreOnce' => 'integer',
        'BankName' => 'string',
        'BankSwiftCode' => 'string',
        'SupplierDiscountPercent' => 'decimal:4',
        'RowCreate' => 'datetime',
        'RowModify' => 'datetime',
        'IsCompany' => 'integer',
        'InvoiceTownship' => 'string',
        'MailTownship' => 'string',
        'GLN' => 'string',
        'PaymentMethodLimitSkip' => 'integer',
        'SupplierPaymentMethod' => 'integer',
        'SupplierPMStrict' => 'integer',
        'SupplierPMToleranceDay' => 'integer',
        'NAVOnlineInvoiceUsername' => 'string',
        'NAVOnlineInvoicePassword' => 'string',
        'NAVOnlineInvoiceSignature' => 'string',
        'NAVOnlineInvoiceDecode' => 'string',
        'NAVOnlineInvoiceInactive' => 'integer',
        'InvoiceCustomer' => 'integer',
        'BuyLimit' => 'decimal:4',
        'ParcelInfo' => 'integer',
        'DiscountPercentDateTime' => 'datetime',
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
        'LookupExE' => 'integer',
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
        'CompanyType' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Code' => 'required|string|max:40',
        'CustomerStatus' => 'required',
        'SupplierStatus' => 'required',
        'Name' => 'required|string|max:100'
    ];

    public function paymentMethodRelation() {
        return $this->belongTo(PaymentMethod::class, 'PaymentMethod', 'Id');
    }

    public function customerCategoryRelation() {
        return $this->belongsTo(CustomerCategory::class, 'CustomerCategory', 'Id');
    }

    public function priceCategoryRelation() {
        return $this->belongsTo(PriceCategory::class, 'PriceCategory', 'Id');
    }

    public function currencyRelation() {
        return $this->belongsTo(Currency::class, 'Currency', 'Id');
    }

    public function customercontactRelation() {
        return $this->hasMany(CustomerContact::class, 'Customer', 'Id');
    }

    public function customerOfferCustomerRelation() {
        return $this->hasMany(CustomerOfferCustomer::class, 'Customer', 'Id');
    }

    public function customerOrderRelation() {
        return $this->hasMany(CustomerOrder::class, 'Customer', 'Id');
    }

    public function logitemRelation() {
        return $this->hasMany(LogItem::class, 'customer_id', 'Id');
    }

    public function productCustomerCodeRelation() {
        return $this->hasMany(ProductCustomerCode::class, 'Customer', 'Id');
    }

    public function shoppingCartRelation() {
        return $this->hasMany(ShoppingCart::class, 'Customer', 'Id');
    }


}
