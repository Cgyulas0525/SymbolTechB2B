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
        Schema::create('customerofferdetail', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('CustomerOffer')->index('IDX_CustomerOfferDetail_CustOffe');
            $table->bigInteger('Product')->index('IDX_CustomerOfferDetail_Product');
            $table->decimal('ShareQuantity', 18, 4)->nullable()->index('IDX_CustomerOfferDetail_SQ');
            $table->bigInteger('Currency')->index('IDX_CustomerOfferDetail_Currency');
            $table->bigInteger('QuantityUnit')->nullable()->index('IDX_CustomerOfferDetail_QUnit');
            $table->decimal('SalesPrice', 18, 4)->default(0);
            $table->decimal('QuantityMinimum', 18, 4)->nullable();
            $table->decimal('QuantityMaximum', 18, 4)->nullable();
            $table->bigInteger('SupplierOfferDetail')->nullable()->index('IDX_CustomerOfferDetail_SuppOff');
            $table->timestamp('RowVersion')->useCurrent();
            $table->integer('RowOrder')->default(0);
            $table->bigInteger('PriceCategory')->nullable();
            $table->decimal('BasePrice', 18, 4)->nullable();
            $table->timestamp('BasePriceDate')->nullable();
            $table->decimal('SalesPercent', 18, 4)->nullable();
            $table->timestamp('RowCreate')->nullable();
            $table->timestamp('RowModify')->nullable();

            $table->index(['Product', 'Currency', 'QuantityUnit'], 'IDX_CustomerOfferDetail_PCQ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customerofferdetail');
    }
};
