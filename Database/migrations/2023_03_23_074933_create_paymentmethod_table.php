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
        Schema::create('paymentmethod', function (Blueprint $table) {
            $table->bigInteger('Id')->index('paymentmethod_Id_index');
            $table->string('Name', 100)->index('IDX_PaymentMethod_Name');
            $table->smallInteger('Cash')->default(0);
            $table->smallInteger('UseAlways')->default(0);
            $table->smallInteger('Immediately')->default(0);
            $table->smallInteger('PettyCashCreation')->default(0);
            $table->integer('ToleranceDay')->nullable();
            $table->smallInteger('FulfillmentTolerance')->default(0);
            $table->decimal('DiscountPercent', 18, 4)->default(0);
            $table->binary('VoucherComment')->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_PaymentMethod');
            $table->timestamp('RowModify')->nullable()->index('IRM_PaymentMethod');

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paymentmethod');
    }
};
