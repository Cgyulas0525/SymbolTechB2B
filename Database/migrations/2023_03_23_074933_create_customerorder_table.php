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
        Schema::create('customerorder', function (Blueprint $table) {
            $table->bigInteger('Id')->index('customerorder_Id_index');
            $table->integer('VoucherType')->default(0)->index('IDX_CustomerOrder_VoucherType');
            $table->bigInteger('VoucherSequence')->index('FK_CustomerOrder_VoucherSeq');
            $table->string('VoucherNumber', 100)->index('UNQ_CustomerOrder_VoucherNumber');
            $table->string('PrimeVoucherNumber', 100)->nullable()->index('IDX_CustomerOrder_Prime');
            $table->bigInteger('CancelledVoucher')->nullable()->index('FK_CustomerOrder_CancelledVch');
            $table->bigInteger('MaintenanceProduct')->nullable();
            $table->bigInteger('Customer')->index('FK_CustomerOrder_Customer');
            $table->bigInteger('CustomerAddress')->nullable()->index('FK_CustomerOrder_CustomerAddres');
            $table->bigInteger('CustomerContact')->nullable()->index('FK_CustomerOrder_CustCont');
            $table->timestamp('VoucherDate')->useCurrent()->index('IDX_CustomerOrder_VoucherDat2');
            $table->timestamp('DeliveryDate')->nullable()->index('IDX_CustomerOrder_DeliveryDat2');
            $table->timestamp('DeliveryFrom')->nullable();
            $table->timestamp('DeliveryTo')->nullable();
            $table->bigInteger('PaymentMethod')->nullable()->index('FK_CustomerOrder_PaymentMethod');
            $table->bigInteger('Currency')->index('FK_CustomerOrder_Currency');
            $table->decimal('CurrencyRate', 18, 4)->default(0);
            $table->bigInteger('Investment')->nullable();
            $table->bigInteger('Division')->nullable()->index('FK_CustomerOrder_Division');
            $table->bigInteger('Agent')->nullable()->index('FK_CustomerOrder_Agent');
            $table->bigInteger('ContactEmployee')->nullable()->index('FK_CustomerOrder_ContactE');
            $table->bigInteger('Campaign')->nullable();
            $table->bigInteger('CustomerContract')->nullable()->index('FK_CustomerOrder_Contract');
            $table->bigInteger('Warehouse')->nullable()->index('FK_CustomerOrder_Warehouse');
            $table->bigInteger('TransportMode')->nullable()->index('FK_CustomerOrder_TransportMode');
            $table->decimal('DepositValue', 18, 4)->nullable()->index('IDX_CustomerOrder_DepValue');
            $table->decimal('DepositPercent', 18, 4)->nullable()->index('IDX_CustomerOrder_DepPercent');
            $table->decimal('NetValue', 18, 4)->nullable();
            $table->decimal('GrossValue', 18, 4)->nullable();
            $table->decimal('VatValue', 18, 4)->nullable();
            $table->bigInteger('AmountAsk')->nullable();
            $table->bigInteger('Maintenance')->nullable();
            $table->smallInteger('SplitForbid')->default(0);
            $table->decimal('PrimePostage', 18, 4)->nullable();
            $table->smallInteger('OrderHidePrice')->default(0);
            $table->smallInteger('Closed')->default(0);
            $table->smallInteger('ClosedManually')->default(0);
            $table->binary('Comment')->nullable();
            $table->smallInteger('Cancelled')->default(0);
            $table->timestamp('RowVersion')->useCurrent();
            $table->bigInteger('MaintOrderSrcCustOrder')->nullable()->index('FK_CustomerOrder_MOSCO');
            $table->timestamp('ExpirationDate')->nullable()->index('IDX_CustomerOrder_Expiratio2');
            $table->binary('InternalComment')->nullable();
            $table->timestamp('FinalizedDate')->nullable();
            $table->bigInteger('ParcelShop')->nullable();
            $table->string('StrExA', 100)->nullable();
            $table->string('StrExB', 100)->nullable();
            $table->string('StrExC', 100)->nullable();
            $table->string('StrExD', 100)->nullable();
            $table->timestamp('DateExA')->nullable();
            $table->timestamp('DateExB')->nullable();
            $table->timestamp('DateExC')->nullable();
            $table->timestamp('DateExD')->nullable();
            $table->decimal('NumExA', 18, 4)->nullable();
            $table->decimal('NumExB', 18, 4)->nullable();
            $table->decimal('NumExC', 18, 4)->nullable();
            $table->decimal('NumExD', 18, 4)->nullable();
            $table->smallInteger('BoolExA')->default(0);
            $table->smallInteger('BoolExB')->default(0);
            $table->smallInteger('BoolExC')->default(0);
            $table->smallInteger('BoolExD')->default(0);
            $table->bigInteger('LookupExA')->nullable();
            $table->bigInteger('LookupExB')->nullable();
            $table->bigInteger('LookupExC')->nullable();
            $table->bigInteger('LookupExD')->nullable();
            $table->binary('MemoExA')->nullable();
            $table->binary('MemoExB')->nullable();
            $table->binary('MemoExC')->nullable();
            $table->binary('MemoExD')->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_CustomerOrder');
            $table->timestamp('RowModify')->nullable()->index('IRM_CustomerOrder');
            $table->smallInteger('NotifyPhone')->default(0);
            $table->smallInteger('NotifySms')->default(0);
            $table->smallInteger('NotifyEmail')->default(0);
            $table->smallInteger('PublicHealthPTFree')->default(0);
            $table->timestamp('FabricDeadLine')->nullable();
            $table->bigInteger('CheckoutBankAccount')->nullable();
            $table->bigInteger('OriginalVoucher')->nullable()->index('FK_CustomerOrder_OriginalV');
            $table->smallInteger('DepositGross')->default(0);
            $table->smallInteger('ExchangePackage')->default(0);
            $table->smallInteger('ChainTransaction')->default(0)->index('IDX_CustomerOrder_ChainTransact');
            $table->timestamp('ValidityDate')->nullable()->index('IDX_CustomerOrder_ValidityDat2');
            $table->timestamp('CurrRateDate')->nullable()->index('IDX_CustomerOrder_CRDat2');
            $table->string('CancelReason', 100)->nullable();
            $table->bigInteger('CustomerOrderStatus')->nullable();
            $table->string('BankTRID', 100)->nullable();
            $table->string('CloseReason', 100)->nullable();
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
            $table->bigInteger('PickerEmployee')->nullable();

            $table->index(['CurrRateDate'], 'IDX_CustomerOrder_CRDate');
            $table->index(['DeliveryDate'], 'IDX_CustomerOrder_DeliveryDate');
            $table->index(['DeliveryFrom', 'DeliveryTo'], 'IDX_CustomerOrder_DeliveryInter');
            $table->index(['ExpirationDate'], 'IDX_CustomerOrder_Expiration');
            $table->index(['ValidityDate'], 'IDX_CustomerOrder_ValidityDate');
            $table->index(['VoucherDate'], 'IDX_CustomerOrder_VoucherDate');
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
        Schema::dropIfExists('customerorder');
    }
};
