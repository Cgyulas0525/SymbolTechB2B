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
        Schema::create('productcategory', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_ProductCategory_Name');
            $table->bigInteger('Parent')->nullable()->index('FK_ProductCategory_Category');
            $table->bigInteger('LeftValue')->default(0);
            $table->bigInteger('RightValue')->default(0);
            $table->decimal('ProfitPercent', 18, 4)->nullable();
            $table->integer('PriceDigits')->nullable();
            $table->string('PriceDigitsExt', 100)->nullable();
            $table->bigInteger('Vat')->nullable()->index('FK_ProductCategory_Vat');
            $table->bigInteger('VatBuy')->nullable()->index('FK_ProductCategory_VatBuy');
            $table->smallInteger('Service')->nullable();
            $table->bigInteger('QuantityUnit')->nullable()->index('FK_ProductCategory_QuantityUnit');
            $table->integer('QuantityDigits')->nullable();
            $table->string('CustomsTariffNumber', 100)->nullable();
            $table->integer('GuaranteeMonths')->nullable();
            $table->bigInteger('GuaranteeMode')->nullable()->index('FK_ProductCategory_GuaranteeMod');
            $table->decimal('GuaranteeMinUnitPrice', 18, 4)->nullable();
            $table->binary('GuaranteeDescription')->nullable();
            $table->string('BarcodeMask', 100)->nullable();
            $table->decimal('MinProfitPercent', 18, 4)->nullable();
            $table->binary('PriceCategoryRule')->nullable();
            $table->binary('VoucherRules')->nullable();
            $table->smallInteger('UseWarrantyRule')->nullable();
            $table->bigInteger('EuVat')->nullable()->index('FK_ProductCategory_EuVat');
            $table->bigInteger('EuVatBuy')->nullable()->index('FK_ProductCategory_EuVatBuy');
            $table->bigInteger('NonEuVat')->nullable()->index('FK_ProductCategory_NonEuVat');
            $table->bigInteger('NonEuVatBuy')->nullable()->index('FK_ProductCategory_NonEuVatBuy');

            $table->index(['LeftValue', 'RightValue'], 'IDX_ProductCategory_Values');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productcategory');
    }
};
