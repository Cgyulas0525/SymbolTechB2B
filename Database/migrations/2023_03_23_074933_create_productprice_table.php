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
        Schema::create('productprice', function (Blueprint $table) {
            $table->bigInteger('Id', true);
            $table->bigInteger('Product');
            $table->bigInteger('Currency')->index('FK_ProductPrice_Currency');
            $table->timestamp('ValidFrom')->useCurrent();
            $table->bigInteger('PriceCategory')->index('FK_ProductPrice_PriceCategory');
            $table->bigInteger('QuantityUnit')->index('FK_ProductPrice_QuantityUnit');
            $table->decimal('Price', 18, 4)->default(0);
            $table->timestamp('RowCreate')->nullable();
            $table->timestamp('RowModify')->nullable();

            $table->index(['Product', 'Currency', 'PriceCategory', 'ValidFrom', 'QuantityUnit'], 'IDX_ProductPrice_PCPV');
            $table->index(['Product', 'RowCreate'], 'productprice_Product_RowCreate_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productprice');
    }
};
