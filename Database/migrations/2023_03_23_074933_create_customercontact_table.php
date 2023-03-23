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
        Schema::create('customercontact', function (Blueprint $table) {
            $table->bigInteger('Id')->index('customercontact_Id_index');
            $table->bigInteger('Customer')->index('FK_CustomerContact_Customer');
            $table->bigInteger('CustomerAddress')->nullable()->index('FK_CustomerContact_CustAddress');
            $table->string('Name', 100)->index('IDX_CustomerContact_Name');
            $table->smallInteger('Theeing')->default(0);
            $table->string('Responsibility', 100)->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('Fax', 20)->nullable();
            $table->string('Sms', 20)->nullable();
            $table->string('Email', 100)->nullable()->index('IDX_CustomerContact_Email');
            $table->string('Url', 100)->nullable();
            $table->string('Skype', 10)->nullable();
            $table->string('FacebookUrl', 100)->nullable();
            $table->string('Msn', 100)->nullable();
            $table->binary('Comment')->nullable();
            $table->binary('VoucherComment')->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowVersion')->useCurrent();
            $table->timestamp('RowCreate')->nullable()->index('IRC_CustomerContact');
            $table->timestamp('RowModify')->nullable()->index('IRM_CustomerContact');

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
        Schema::dropIfExists('customercontact');
    }
};
