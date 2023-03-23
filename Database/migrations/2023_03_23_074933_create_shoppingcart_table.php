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
        Schema::create('shoppingcart', function (Blueprint $table) {
            $table->bigInteger('Id', true)->unique('IDX_ShoppingCart_Closed');
            $table->string('VoucherNumber', 100)->nullable()->index('shoppingcart_VoucherNumber_index');
            $table->bigInteger('Customer')->index('FK_ShoppingCart_Customer');
            $table->bigInteger('CustomerAddress')->nullable()->index('FK_ShoppingCart_CustomerAddres');
            $table->bigInteger('CustomerContact')->nullable()->index('shoppingcart_pk');
            $table->timestamp('VoucherDate')->useCurrent()->index('IDX_ShoppingCart_VoucherDate');
            $table->timestamp('DeliveryDate')->nullable()->index('IDX_ShoppingCart_DeliveryDate');
            $table->bigInteger('PaymentMethod')->nullable()->index('FK_ShoppingCart_PaymentMethod');
            $table->bigInteger('Currency')->index('FK_ShoppingCart_Currency');
            $table->decimal('CurrencyRate', 18, 4)->default(0);
            $table->bigInteger('CustomerContract')->nullable()->index('FK_ShoppingCart_Contract');
            $table->bigInteger('TransportMode')->nullable()->index('FK_ShoppingCart_TransportMode');
            $table->decimal('DepositValue', 18, 4)->nullable()->index('IDX_ShoppingCart_DepValue');
            $table->decimal('DepositPercent', 18, 4)->nullable()->index('IDX_ShoppingCart_DepPercent');
            $table->decimal('NetValue', 18, 4)->nullable();
            $table->decimal('GrossValue', 18, 4)->nullable();
            $table->decimal('VatValue', 18, 4)->nullable();
            $table->binary('Comment')->nullable();
            $table->smallInteger('Opened')->default(0);
            $table->bigInteger('CustomerOrder')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['Id']);
            $table->index(['Opened', 'CustomerContact'], 'shoppingcart_Opened_CustomerContact_index');
            $table->unique(['VoucherNumber'], 'UNQ_ShoppingCart_VoucherNumber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shoppingcart');
    }
};
