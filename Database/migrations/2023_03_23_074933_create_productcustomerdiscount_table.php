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
        Schema::create('productcustomerdiscount', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('Product');
            $table->bigInteger('Customer')->index('FK_ProductCustomerDiscount_Cust');
            $table->decimal('Discount', 18, 4)->default(0);

            $table->unique(['Product', 'Customer'], 'UNQ_ProductCustomerDiscount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productcustomerdiscount');
    }
};
