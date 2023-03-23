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
        Schema::create('productcustomercode', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('Product');
            $table->bigInteger('Customer')->index('FK_ProductCustomerCode_Customer');
            $table->string('Code', 40)->index('IDX_ProductCustomerCode_Code');
            $table->string('Name', 100)->nullable()->index('IDX_ProductCustomerCode_Name');
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductCustomerCode');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductCustomerCode');

            $table->unique(['Product', 'Customer'], 'UNQ_ProductCustomerCode_ProdCus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productcustomercode');
    }
};
