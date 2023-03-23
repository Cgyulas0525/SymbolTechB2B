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
        Schema::create('customeraddress', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('Customer')->index('FK_CustomerAddress_Customer');
            $table->smallInteger('Preferred')->default(0);
            $table->string('Code', 40)->nullable()->index('IDX_CustomerAddress_Code');
            $table->string('Name', 100)->index('IDX_CustomerAddress_Name');
            $table->smallInteger('DisplayCountry')->default(0);
            $table->string('Country', 100)->nullable();
            $table->string('Region', 100)->nullable();
            $table->string('Zip', 10)->nullable();
            $table->string('City', 100)->nullable();
            $table->string('Street', 100)->nullable();
            $table->string('HouseNumber', 20)->nullable();
            $table->string('ContactName', 100)->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('Fax', 20)->nullable();
            $table->string('Email', 100)->nullable()->index('IDX_CustomerAddress_Email');
            $table->smallInteger('IsCompany')->default(0);
            $table->string('CompanyTaxNumber', 20)->nullable();
            $table->binary('DeliveryInfo')->nullable();
            $table->binary('Comment')->nullable();
            $table->binary('VoucherComment')->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowVersion')->useCurrent();
            $table->bigInteger('PaymentMethod')->nullable()->index('FK_CustomerAddress_PaymentMet');
            $table->bigInteger('TransportMode')->nullable()->index('FK_CustomerAddress_TransportM');
            $table->integer('DeliveryCDay')->nullable();
            $table->bigInteger('Agent')->nullable()->index('FK_CustomerAddress_Agent');
            $table->smallInteger('AgentStrict')->default(0);
            $table->string('Sms', 20)->nullable();
            $table->string('CompanyEUTaxNumber', 20)->nullable();
            $table->string('CompanyGroupTaxNumber', 20)->nullable();
            $table->string('CompanyTradeRegNumber', 20)->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_CustomerAddress');
            $table->timestamp('RowModify')->nullable()->index('IRM_CustomerAddress');
            $table->string('Township', 100)->nullable();
            $table->string('GLN', 40)->nullable();
            $table->smallInteger('IsPerson')->default(0);
            $table->bigInteger('ParcelInfo')->nullable();
            $table->integer('EUMembership')->nullable();
            $table->integer('CompanyType')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customeraddress');
    }
};
