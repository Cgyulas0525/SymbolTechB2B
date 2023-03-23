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
        Schema::create('customercategory', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_CustomerCategory_Name');
            $table->bigInteger('Parent')->nullable()->index('FK_CustomerCategory_Parent');
            $table->bigInteger('LeftValue')->default(0);
            $table->bigInteger('RightValue')->default(0);
            $table->decimal('DiscountPercent', 18, 4)->nullable();
            $table->bigInteger('PaymentMethod')->nullable()->index('FK_CustomerCategory_PM');
            $table->smallInteger('PaymentMethodStrict')->default(0);
            $table->bigInteger('PriceCategory')->nullable()->index('FK_CustomerCategory_PC');
            $table->bigInteger('Currency')->nullable()->index('FK_CustomerCategory_Curr');
            $table->bigInteger('TransportMode')->nullable()->index('FK_CustomerCategory_TrMode');
            $table->binary('VoucherRules')->nullable();
            $table->binary('DebitQuota')->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_CustomerCategory');
            $table->timestamp('RowModify')->nullable()->index('IRM_CustomerCategory');
            $table->smallInteger('IsCompany')->nullable();

            $table->index(['LeftValue', 'RightValue'], 'IDX_CustomerCategory_Value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customercategory');
    }
};
