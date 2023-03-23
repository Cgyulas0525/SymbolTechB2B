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
        Schema::create('employee', function (Blueprint $table) {
            $table->bigInteger('Id', true);
            $table->bigInteger('Site')->nullable()->index('IDX_Employee_Site');
            $table->smallInteger('IsAdmin')->default(0);
            $table->smallInteger('IsEmployee')->default(0)->index('IDX_Employee_IsEmployee');
            $table->smallInteger('IsPermission')->default(0);
            $table->smallInteger('IsAgent')->default(0);
            $table->string('Code', 40)->nullable()->index('IDX_Employee_Code');
            $table->string('Titular', 10)->nullable();
            $table->string('Name', 80)->index('IDX_Employee_Name');
            $table->string('BirthName', 100)->nullable();
            $table->string('BirthPlace', 100)->nullable();
            $table->dateTime('BirthDate')->nullable();
            $table->smallInteger('GenderMale')->default(0);
            $table->string('Nationality', 100)->nullable();
            $table->string('MotherName', 100)->nullable();
            $table->string('TaxId', 20)->nullable()->index('IDX_Employee_TaxId');
            $table->string('InsuranceId', 20)->nullable()->index('IDX_Employee_InsId');
            $table->string('IdentifiyNumber', 20)->nullable()->index('IDX_Employee_IdentifiyNumber');
            $table->string('PassportNumber', 20)->nullable()->index('IDX_Employee_PassportNumber');
            $table->string('BankName', 100)->nullable();
            $table->string('BankAccount', 100)->nullable();
            $table->string('Phone', 100)->nullable();
            $table->string('Sms', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->string('PhonePrivate', 100)->nullable();
            $table->string('SmsPrivate', 20)->nullable();
            $table->string('EmailPrivate', 100)->nullable();
            $table->binary('Picture')->nullable();
            $table->bigInteger('DefaultDivision')->nullable()->index('FK_Employee_DefaultDivision');
            $table->bigInteger('Leader')->nullable()->index('FK_Employee_Leader');
            $table->smallInteger('LoginDisabled')->default(0);
            $table->string('Username', 32)->nullable();
            $table->string('Password', 32)->nullable();
            $table->string('PINCode', 10)->nullable()->index('IDX_Employee_PINCode');
            $table->string('SidSddl', 128)->nullable();
            $table->string('SidSddlMachine', 128)->nullable();
            $table->string('TwoFactorAuthSms', 20)->nullable();
            $table->string('TwoFactorAuthEmail', 100)->nullable();
            $table->binary('EmailSignature')->nullable();
            $table->string('UILanguage', 10)->nullable();
            $table->binary('CallCardInfo')->nullable();
            $table->binary('Setting')->nullable();
            $table->string('FabricExpense', 100)->nullable();
            $table->binary('Comment')->nullable();
            $table->smallInteger('Deleted')->default(0);

            $table->index(['Username', 'Password'], 'IDX_Employee_UserPass');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee');
    }
};
