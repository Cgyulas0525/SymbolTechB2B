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
        Schema::create('customerorderdetail', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('CustomerOrder')->index('FK_CustomerOrderDetail_Order');
            $table->bigInteger('CancelledDetail')->nullable()->index('FK_CustomerOrderDetail_Cancel');
            $table->timestamp('DeliveryDate')->nullable()->index('IDX_CustomerOrderDetail_DelDat2');
            $table->timestamp('DeliveryFrom')->nullable();
            $table->timestamp('DeliveryTo')->nullable();
            $table->bigInteger('Currency')->index('FK_CustomerOrderDetail_Currency');
            $table->decimal('CurrencyRate', 18, 4)->default(0);
            $table->bigInteger('Investment')->nullable();
            $table->bigInteger('Division')->nullable()->index('FK_CustomerOrderDetail_Division');
            $table->bigInteger('Agent')->nullable()->index('FK_CustomerOrderDetail_Agent');
            $table->bigInteger('Campaign')->nullable();
            $table->bigInteger('Product')->nullable()->index('FK_CustomerOrderDetail_Product');
            $table->string('ProductAlias', 100)->nullable();
            $table->bigInteger('MaintenanceProduct')->nullable();
            $table->string('Keywords', 100)->nullable()->index('IDX_CustomerOrderDetail_Keyword');
            $table->bigInteger('Vat')->nullable()->index('FK_CustomerOrderDetail_Vat');
            $table->bigInteger('QuantityUnit')->nullable()->index('FK_CustomerOrderDetail_QUnit');
            $table->decimal('QURate', 18, 4)->default(0);
            $table->decimal('Quantity', 18, 4)->default(0);
            $table->decimal('FulfilledQuantity', 18, 4)->default(0);
            $table->decimal('CancelledQuantity', 18, 4)->default(0);
            $table->decimal('CompleteQuantity', 18, 4)->default(0);
            $table->bigInteger('DetailStatus')->nullable()->index('FK_CustomerOrderDetail_DetailSt');
            $table->bigInteger('CustomerOfferDetail')->nullable();
            $table->bigInteger('CustomerContractDetail')->nullable();
            $table->smallInteger('AllocateWarehouse')->default(0);
            $table->smallInteger('MustMunufacturing')->default(0);
            $table->decimal('ManufacQuantity', 18, 4)->default(0);
            $table->decimal('UnitPrice', 18, 4)->nullable();
            $table->decimal('DiscountPercent', 18, 4)->nullable();
            $table->decimal('DiscountUnitPrice', 18, 4)->nullable();
            $table->smallInteger('GrossPrices')->default(0);
            $table->decimal('DepositValue', 18, 4)->nullable()->index('IDX_CustomerOrderDetail_DepVal');
            $table->decimal('DepositPercent', 18, 4)->nullable()->index('IDX_CustomerOrderDetail_DepPerc');
            $table->decimal('NetValue', 18, 4)->nullable();
            $table->decimal('GrossValue', 18, 4)->nullable();
            $table->decimal('VatValue', 18, 4)->nullable();
            $table->binary('Comment')->nullable();
            $table->smallInteger('CopyCommentToInvoice')->default(0);
            $table->integer('RowOrder')->default(0)->index('IDX_CustomerOrderDetail_RowOrd');
            $table->timestamp('RowVersion')->useCurrent();
            $table->smallInteger('Reverse')->default(0);
            $table->binary('InternalComment')->nullable();
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
            $table->timestamp('RowCreate')->nullable()->index('IRC_CustomerOrderDetail');
            $table->timestamp('RowModify')->nullable()->index('IRM_CustomerOrderDetail');
            $table->bigInteger('FabricSchema')->nullable();
            $table->decimal('PublicHealthPTUPrice', 18, 4)->nullable();
            $table->timestamp('FabricDeadLine')->nullable();
            $table->bigInteger('PriceCategory')->nullable()->index('FK_CustomerOrderDetail_PC');
            $table->timestamp('CurrRateDate')->nullable()->index('IDX_CustomerOrderDetail_CRDat2');
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
            $table->integer('RowPosition')->nullable();
            $table->bigInteger('OriginalVoucher')->nullable();
            $table->string('PickingNumber', 100)->nullable();
            $table->string('ParcelIdentifier', 100)->nullable();
            $table->timestamp('SupplierDeliveryDate')->nullable();
            $table->decimal('SupplierQuantity', 18, 4)->nullable();

            $table->index(['CurrRateDate'], 'IDX_CustomerOrderDetail_CRDate');
            $table->index(['DeliveryDate'], 'IDX_CustomerOrderDetail_DelDate');
            $table->index(['DeliveryFrom', 'DeliveryTo'], 'IDX_CustomerOrderDetail_DelInt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customerorderdetail');
    }
};
