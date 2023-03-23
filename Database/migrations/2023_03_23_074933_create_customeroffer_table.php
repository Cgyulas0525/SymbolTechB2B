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
        Schema::create('customeroffer', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('VoucherSequence')->index('IDX_CustomerOffer_VoucherSequenc');
            $table->string('VoucherNumber', 100)->index('IDX_CustomerOffer_VoucherNumber');
            $table->string('Name', 100)->nullable()->index('IDX_CustomerOffer_Name');
            $table->timestamp('ValidFrom')->useCurrent();
            $table->timestamp('ValidTo')->useCurrent();
            $table->smallInteger('OrderDlvFrom')->default(0);
            $table->smallInteger('OrderDlvTo')->default(0);
            $table->bigInteger('Campaign')->nullable();
            $table->integer('CustomerDepend')->default(0);
            $table->timestamp('RowVersion')->useCurrent();

            $table->index(['ValidFrom', 'ValidTo'], 'IDX_CustomerOffer_Valids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customeroffer');
    }
};
