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
        Schema::create('product', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Code', 40)->unique('UNQ_Product_Code');
            $table->smallInteger('CodeHidden')->default(0);
            $table->string('Barcode', 100)->nullable()->index('IDX_Product_Barcode');
            $table->string('Name', 100)->index('IDX_Product_Name');
            $table->smallInteger('Inactive')->default(0);
            $table->timestamp('CreateDateTime')->nullable()->useCurrent();
            $table->bigInteger('PrimeSupplier')->nullable()->index('FK_Product_PrimeSupplier');
            $table->bigInteger('Manufacturer')->nullable();
            $table->bigInteger('ProductCategory')->nullable()->index('FK_Product_ProductCategory');
            $table->bigInteger('Vat')->nullable()->index('FK_Product_Vat');
            $table->bigInteger('VatBuy')->nullable();
            $table->smallInteger('SellBanned')->default(0);
            $table->smallInteger('BuyBanned')->default(0);
            $table->smallInteger('RunOut')->default(0);
            $table->smallInteger('Service')->default(0);
            $table->smallInteger('MediateService')->default(0);
            $table->smallInteger('ZeroPriceAllowed')->default(0);
            $table->smallInteger('Accumulator')->default(0);
            $table->bigInteger('AccProduct')->nullable()->index('FK_Product_AccProduct');
            $table->smallInteger('VisibleInPriceList')->default(0);
            $table->bigInteger('QuantityUnit')->nullable()->index('FK_Product_QuantityUnit');
            $table->integer('QuantityDigits')->default(0);
            $table->integer('PriceDigits')->default(0);
            $table->string('PriceDigitsExt', 100)->nullable();
            $table->smallInteger('GrossPrices')->default(0);
            $table->smallInteger('SupplierPriceAffected')->default(0);
            $table->integer('SupplierPriceTolerance')->default(0);
            $table->smallInteger('SupplierInPriceOnly')->default(0);
            $table->smallInteger('SupplierToSysCurrency')->nullable();
            $table->smallInteger('SupplierToBaseQU')->default(0);
            $table->smallInteger('WeightControll')->default(0);
            $table->smallInteger('AttachmentRoll')->default(0);
            $table->string('CustomsTariffNumber', 100)->nullable()->index('IDX_Product_CustomTariffNumber');
            $table->decimal('Weight', 18, 4)->nullable();
            $table->decimal('DimensionWidth', 18, 4)->nullable();
            $table->decimal('DimensionHeight', 18, 4)->nullable();
            $table->decimal('DimensionDepth', 18, 4)->nullable();
            $table->decimal('QuantityMin', 18, 4)->nullable();
            $table->decimal('QuantityMax', 18, 4)->nullable();
            $table->decimal('QuantityOpt', 18, 4)->nullable();
            $table->decimal('QtyPackage', 18, 4)->nullable();
            $table->decimal('QtyLevel', 18, 4)->nullable();
            $table->decimal('QtyPallet', 18, 4)->nullable();
            $table->bigInteger('IstatKN')->nullable();
            $table->bigInteger('IstatCountryOrigin')->nullable();
            $table->decimal('IncidentExpense', 18, 4)->nullable();
            $table->decimal('IncidentExpensePerc', 18, 4)->nullable();
            $table->integer('GuaranteeMonths')->nullable();
            $table->bigInteger('GuaranteeMode')->nullable()->index('FK_Product_GuaranteeMode');
            $table->decimal('GuaranteeMinUnitPrice', 18, 4)->nullable();
            $table->integer('BestBeforeValue')->nullable();
            $table->smallInteger('BestBeforeIsDay')->default(0);
            $table->binary('PriceCategoryRule')->nullable();
            $table->smallInteger('MustMunufacturing')->default(0);
            $table->smallInteger('StrictManufacturing')->default(0);
            $table->integer('SerialMode')->default(0);
            $table->binary('SerialSetting')->nullable();
            $table->integer('ShelfMode')->default(0);
            $table->smallInteger('ClearAllocation')->default(0);
            $table->string('DefaultAlias', 100)->nullable();
            $table->decimal('DepositPercent', 18, 4)->nullable()->index('IDX_Product_DepPercent');
            $table->string('Pictogram', 100)->nullable();
            $table->binary('Comment')->nullable();
            $table->string('WebName', 100)->nullable();
            $table->binary('WebDescription')->nullable();
            $table->string('WebUrl', 100)->nullable();
            $table->binary('Picture')->nullable();
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
            $table->bigInteger('LookupExA')->nullable()->index('FK_Product_ExA');
            $table->bigInteger('LookupExB')->nullable()->index('FK_Product_ExB');
            $table->bigInteger('LookupExC')->nullable()->index('FK_Product_ExC');
            $table->bigInteger('LookupExD')->nullable()->index('FK_Product_ExD');
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowVersion')->nullable()->useCurrent();
            $table->decimal('MinProfitPercent', 18, 4)->nullable();
            $table->decimal('ManufacturingCost', 18, 4)->nullable();
            $table->smallInteger('SerialAutoMaintenance')->default(0);
            $table->bigInteger('AdrMaterial')->nullable();
            $table->bigInteger('AdrPackage')->nullable();
            $table->decimal('WeightNet', 18, 4)->nullable();
            $table->binary('MemoExA')->nullable();
            $table->binary('MemoExB')->nullable();
            $table->timestamp('DateExC')->nullable();
            $table->timestamp('DateExD')->nullable();
            $table->decimal('NumExD', 18, 4)->nullable();
            $table->smallInteger('BoolExC')->default(0);
            $table->smallInteger('BoolExD')->default(0);
            $table->binary('MemoExC')->nullable();
            $table->binary('MemoExD')->nullable();
            $table->binary('WebMetaDescription')->nullable();
            $table->string('WebKeywords', 100)->nullable();
            $table->smallInteger('WebDisplay')->default(0);
            $table->bigInteger('LookupExE')->nullable()->index('FK_Product_ExE');
            $table->timestamp('RowCreate')->nullable()->index('IRC_Product');
            $table->timestamp('RowModify')->nullable()->index('IRM_Product');
            $table->decimal('FillingVolume', 18, 4)->nullable();
            $table->bigInteger('PublicHealthPT')->nullable();
            $table->binary('VoucherRules')->nullable();
            $table->smallInteger('IsLarge')->default(0);
            $table->smallInteger('UseWarrantyRule')->default(0)->index('IDX_Product_UseWarrantyRule');
            $table->integer('AdrCalcBasis')->default(0);
            $table->bigInteger('EuVat')->nullable();
            $table->bigInteger('EuVatBuy')->nullable()->index('FK_Product_EuVatBuy');
            $table->bigInteger('NonEuVat')->nullable()->index('FK_Product_NonEuVat');
            $table->bigInteger('NonEuVatBuy')->nullable()->index('FK_Product_NonEuVatBuy');
            $table->smallInteger('BidAllowed')->default(0);
            $table->smallInteger('IsPallet')->default(0);
            $table->smallInteger('IsFragile')->nullable()->default(0);
            $table->timestamp('PictureDateTime')->nullable();
            $table->decimal('MinSellQuantity', 18, 4)->nullable();
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
            $table->decimal('MinSellPrice', 18, 4)->nullable();
            $table->decimal('MinSellBelowPrice', 18, 4)->nullable();

            $table->unique(['Id'], 'product_Id_uindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
};
