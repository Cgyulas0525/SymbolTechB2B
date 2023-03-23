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
        Schema::create('paymentmethodlang', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->integer('Lang')->default(0);
            $table->bigInteger('PaymentMethod')->index('FK_PaymentMethodLang_PM');
            $table->string('Name', 100);
            $table->binary('VoucherComment')->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_PaymentMethodLang');
            $table->timestamp('RowModify')->nullable()->index('IRM_PaymentMethodLang');

            $table->index(['Lang', 'PaymentMethod'], 'IDX_PaymentMethodLang_LPM');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paymentmethodlang');
    }
};
