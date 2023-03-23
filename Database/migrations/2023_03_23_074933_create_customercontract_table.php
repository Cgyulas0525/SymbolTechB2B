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
        Schema::create('customercontract', function (Blueprint $table) {
            $table->bigInteger('Id')->unique('PK_CustomerContract');
            $table->bigInteger('VoucherSequence')->index('FK_CustomerContract_Sequence');
            $table->string('VoucherNumber', 100)->index('UNQ_CustomerContract_VoucherNum');
            $table->string('PrimeVoucherNumber', 100)->nullable()->index('IDX_CustomerContract_Prime');
            $table->bigInteger('Customer')->index('FK_CustomerContract_Customer');
            $table->smallInteger('AddressDepends')->default(0);
            $table->bigInteger('CustomerAddress')->nullable()->index('FK_CustomerContract_CAddress');
            $table->string('Subject', 100)->nullable();
            $table->timestamp('ValidFrom')->useCurrent();
            $table->timestamp('ValidTo')->nullable();
            $table->binary('InvoiceOccurence')->nullable();
            $table->timestamp('AlertGenerated')->nullable();
            $table->bigInteger('PaymentMethod')->nullable()->index('FK_CustomerContract_PaymentMet');
            $table->smallInteger('SuppressPriceAffect')->default(0);
            $table->smallInteger('OfferOverride')->default(0);
            $table->smallInteger('ManualAdapt')->default(0);
            $table->binary('Comment')->nullable();
            $table->smallInteger('CopyCommentToInvoice')->default(0);
            $table->smallInteger('Cancelled')->default(0);
            $table->timestamp('RowVersion')->useCurrent();
            $table->smallInteger('InvoiceModeSeason')->default(0);
            $table->bigInteger('Investment')->nullable()->index('FK_CustomerContract_Inv');

            $table->index(['ValidFrom', 'ValidTo'], 'IDX_CustomerContract_Valids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customercontract');
    }
};
