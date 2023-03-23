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
        Schema::create('shoppingcartdetail', function (Blueprint $table) {
            $table->bigInteger('Id', true);
            $table->bigInteger('ShoppingCart');
            $table->bigInteger('Currency')->index('FK_ShoppingCartDetail_Currency');
            $table->decimal('CurrencyRate', 18, 4)->default(0);
            $table->bigInteger('Product')->nullable()->index('FK_ShoppingCartDetail_Product');
            $table->bigInteger('Vat')->nullable()->index('FK_ShoppingCartDetail_Vat');
            $table->bigInteger('QuantityUnit')->nullable()->index('FK_ShoppingCartDetail_QUnit');
            $table->smallInteger('Reverse')->default(0);
            $table->decimal('Quantity', 18, 4)->default(0);
            $table->bigInteger('CustomerOfferDetail')->nullable()->index('FK_ShoppingCartDetail_CustOffD');
            $table->bigInteger('CustomerContractDetail')->nullable()->index('FK_ShoppingCartDetail_CustConD');
            $table->decimal('UnitPrice', 18, 4)->nullable();
            $table->decimal('DiscountPercent', 18, 4)->nullable();
            $table->decimal('DiscountUnitPrice', 18, 4)->nullable();
            $table->smallInteger('GrossPrices')->default(0);
            $table->decimal('DepositValue', 18, 4)->nullable()->unique('IDX_ShoppingCartDetail_DepVal');
            $table->decimal('DepositPercent', 18, 4)->nullable()->unique('IDX_ShoppingCartDetail_DepPerc');
            $table->decimal('NetValue', 18, 4)->nullable();
            $table->decimal('GrossValue', 18, 4)->nullable();
            $table->decimal('VatValue', 18, 4)->nullable();
            $table->binary('Comment')->nullable();
            $table->bigInteger('CustomerOrderDetail')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ShoppingCart', 'Id'], 'shoppingcartdetail_ShoppingCart_Id_index');
            $table->unique(['ShoppingCart', 'Id'], 'shoppingcartdetail_ShoppingCart_Id_uindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shoppingcartdetail');
    }
};
