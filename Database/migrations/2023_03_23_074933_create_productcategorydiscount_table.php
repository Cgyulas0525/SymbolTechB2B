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
        Schema::create('productcategorydiscount', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('ProductCategory')->index('FK_ProductCategoryDiscount_PrCa');
            $table->bigInteger('Customer')->nullable()->index('FK_Product_EuVat');
            $table->bigInteger('CustCategory')->nullable()->index('FK_ProductCategoryDiscount_CCat');
            $table->smallInteger('Inherit')->default(0);
            $table->decimal('Discount', 18, 4)->default(0);
            $table->timestamp('ValidFrom')->nullable();
            $table->timestamp('ValidTo')->nullable();

            $table->index(['ValidFrom', 'ValidTo'], 'IDX_ProductCategoryDiscount_Vs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productcategorydiscount');
    }
};
