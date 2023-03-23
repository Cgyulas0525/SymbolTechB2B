<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->bigInteger('Id')->unique('customer_Id_uindex');
            $table->string('Code', 40)->index('UNQ_Customer_Code');
            $table->smallInteger('CustomerStatus')->default(0);
            $table->smallInteger('SupplierStatus')->default(0);
            $table->string('Name', 100)->index('IDX_Customer_Name');
            $table->string('SearchName', 100)->nullable()->index('IDX_Customer_SearchName');
            $table->timestamp('CreateDateTime')->useCurrent();
            $table->bigInteger('CustomerCategory')->nullable();
            $table->bigInteger('SupplierCategory')->nullable()->index('FK_Customer_SupplierCategory');
            $table->smallInteger('DisplayCountry')->default(0);
            $table->string('InvoiceCountry', 100)->nullable();
            $table->string('InvoiceRegion', 100)->nullable();
            $table->string('InvoiceZip', 10)->nullable();
            $table->string('InvoiceCity', 100)->nullable();
            $table->string('InvoiceStreet', 100)->nullable();
            $table->string('InvoiceHouseNumber', 20)->nullable();
            $table->smallInteger('MailBanned')->default(0);
            $table->string('MailCountry', 100)->nullable();
            $table->string('MailRegion', 100)->nullable();
            $table->string('MailName', 100)->nullable();
            $table->smallInteger('MailOriginalName')->default(0);
            $table->string('MailZip', 10)->nullable();
            $table->string('MailCity', 100)->nullable();
            $table->string('MailStreet', 100)->nullable();
            $table->string('MailHouseNumber', 20)->nullable();
            $table->bigInteger('PaymentMethod')->nullable()->index('FK_Customer_PaymentMethod');
            $table->smallInteger('PaymentMethodStrict')->default(0);
            $table->integer('PaymentMethodToleranceDay')->nullable();
            $table->bigInteger('PriceCategory')->nullable()->index('FK_Customer_PriceCategory');
            $table->bigInteger('CustomerIstatTemplate')->nullable();
            $table->bigInteger('SupplierIstatTemplate')->nullable();
            $table->bigInteger('Currency')->nullable();
            $table->bigInteger('TransportMode')->nullable()->index('FK_Customer_TransportMode');
            $table->string('TradeRegNumber', 20)->nullable();
            $table->string('TaxNumber', 20)->nullable();
            $table->string('EUTaxNumber', 20)->nullable();
            $table->string('GroupTaxNUmber', 20)->nullable();
            $table->integer('EUMembership')->default(0);
            $table->string('BankAccount', 100)->nullable();
            $table->string('BankAccountIBAN', 100)->nullable();
            $table->string('ContactName', 100)->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('Fax', 20)->nullable();
            $table->string('Sms', 20)->nullable();
            $table->string('Email', 100)->nullable()->index('IDX_Customer_Email');
            $table->smallInteger('RobinsonMode')->default(0);
            $table->smallInteger('AllowEmailVouchers')->default(0);
            $table->smallInteger('SpecVoucherEmails')->default(0);
            $table->string('WebUsername', 100)->nullable()->index('IDX_Customer_WebUsername');
            $table->string('WebPassword', 100)->nullable();
            $table->binary('DeliveryInfo')->nullable();
            $table->binary('Comment')->nullable();
            $table->binary('VoucherRules')->nullable();
            $table->decimal('DiscountPercent', 18, 4)->default(0);
            $table->binary('DebitQuota')->nullable();
            $table->binary('EInvoice')->nullable();
            $table->string('BuyCompanyCode', 40)->nullable();
            $table->string('SellCompanyCode', 40)->nullable();
            $table->bigInteger('Agent')->nullable()->index('FK_Customer_Agent');
            $table->smallInteger('AgentStrict')->default(0);
            $table->string('StrExA', 100)->nullable();
            $table->string('StrExB', 100)->nullable();
            $table->string('StrExC', 100)->nullable();
            $table->string('StrExD', 100)->nullable();
            $table->timestamp('DateExA')->nullable();
            $table->timestamp('DateExB')->nullable();
            $table->decimal('NumExA', 18, 4)->nullable();
            $table->decimal('NumExB', 18, 4)->nullable();
            $table->decimal('NumExC', 18, 4)->nullable();
            $table->smallInteger('BoolExA')->default(0);
            $table->smallInteger('BoolExB')->default(0);
            $table->bigInteger('LookupExA')->nullable()->index('FK_Customer_ExA');
            $table->bigInteger('LookupExB')->nullable()->index('FK_Customer_ExB');
            $table->bigInteger('LookupExC')->nullable()->index('FK_Customer_ExC');
            $table->bigInteger('LookupExD')->nullable()->index('FK_Customer_ExD');
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowVersion')->useCurrent();
            $table->integer('DeliveryCDay')->nullable();
            $table->integer('DeliverySDay')->nullable();
            $table->smallInteger('SelfSupplierInvoice')->default(0);
            $table->string('Url', 100)->nullable();
            $table->binary('MemoExA')->nullable();
            $table->binary('MemoExB')->nullable();
            $table->timestamp('DateExC')->nullable();
            $table->timestamp('DateExD')->nullable();
            $table->decimal('NumExD', 18, 4)->nullable();
            $table->smallInteger('BoolExC')->default(0);
            $table->smallInteger('BoolExD')->default(0);
            $table->binary('MemoExC')->nullable();
            $table->binary('MemoExD')->nullable();
            $table->binary('SupplierDebitQuota')->nullable();
            $table->smallInteger('DebitQIgnoreOnce')->default(0);
            $table->string('BankName', 100)->nullable()->index('IDX_Customer_BankName');
            $table->string('BankSwiftCode', 100)->nullable()->index('IDX_Customer_BankSwiftCode');
            $table->decimal('SupplierDiscountPercent', 18, 4)->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_Customer');
            $table->timestamp('RowModify')->nullable()->index('IRM_Customer');
            $table->smallInteger('IsCompany')->default(0);
            $table->string('InvoiceTownship', 100)->nullable();
            $table->string('MailTownship', 100)->nullable();
            $table->string('GLN', 40)->nullable();
            $table->smallInteger('PaymentMethodLimitSkip')->default(0);
            $table->bigInteger('SupplierPaymentMethod')->nullable()->index('FK_Customer_SupplierPM');
            $table->smallInteger('SupplierPMStrict')->default(0);
            $table->integer('SupplierPMToleranceDay')->nullable();
            $table->string('NAVOnlineInvoiceUsername', 100)->nullable();
            $table->string('NAVOnlineInvoicePassword', 100)->nullable();
            $table->string('NAVOnlineInvoiceSignature', 100)->nullable();
            $table->string('NAVOnlineInvoiceDecode', 100)->nullable();
            $table->smallInteger('NAVOnlineInvoiceInactive')->default(0);
            $table->bigInteger('InvoiceCustomer')->nullable()->index('FK_Customer_InvoiceCustomer');
            $table->decimal('BuyLimit', 18, 4)->nullable()->index('IDX_Customer_BuyLimit');
            $table->bigInteger('ParcelInfo')->nullable();
            $table->timestamp('DiscountPercentDateTime')->nullable();
            $table->string('StrExE', 100)->nullable();
            $table->string('StrExF', 100)->nullable();
            $table->string('StrExG', 100)->nullable();
            $table->string('StrExH', 100)->nullable();
            $table->string('StrExI', 100)->nullable();
            $table->string('StrExJ', 100)->nullable();
            $table->timestamp('DateExE')->nullable();
            $table->timestamp('DateExF')->nullable();
            $table->timestamp('DateExG')->nullable();
            $table->timestamp('DateExH')->nullable();
            $table->timestamp('DateExI')->nullable();
            $table->timestamp('DateExJ')->nullable();
            $table->decimal('NumExE', 18, 4)->nullable();
            $table->decimal('NumExF', 18, 4)->nullable();
            $table->decimal('NumExG', 18, 4)->nullable();
            $table->decimal('NumExH', 18, 4)->nullable();
            $table->decimal('NumExI', 18, 4)->nullable();
            $table->decimal('NumExJ', 18, 4)->nullable();
            $table->smallInteger('BoolExE')->nullable();
            $table->smallInteger('BoolExF')->nullable();
            $table->smallInteger('BoolExG')->nullable();
            $table->smallInteger('BoolExH')->nullable();
            $table->smallInteger('BoolExI')->nullable();
            $table->smallInteger('BoolExJ')->nullable();
            $table->bigInteger('LookupExE')->nullable();
            $table->bigInteger('LookupExF')->nullable();
            $table->bigInteger('LookupExG')->nullable();
            $table->bigInteger('LookupExH')->nullable();
            $table->bigInteger('LookupExI')->nullable();
            $table->bigInteger('LookupExJ')->nullable();
            $table->binary('MemoExE')->nullable();
            $table->binary('MemoExF')->nullable();
            $table->binary('MemoExG')->nullable();
            $table->binary('MemoExH')->nullable();
            $table->binary('MemoExI')->nullable();
            $table->binary('MemoExJ')->nullable();
            $table->integer('CompanyType')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer');
    }
};
