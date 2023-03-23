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
        Schema::create('customeroffercustomer', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('CustomerOffer')->index('IDX_CustomerOfferCustomer_Offer');
            $table->bigInteger('Customer')->nullable()->index('IDX_CustomerOfferCustomer_Cust');
            $table->bigInteger('CustomerCategory')->nullable()->index('IDX_CustomerOfferCustomer_1');
            $table->smallInteger('Forbid')->default(0);

            $table->index(['Customer'], 'IDX_CustomerOfferCustomer_Customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customeroffercustomer');
    }
};
