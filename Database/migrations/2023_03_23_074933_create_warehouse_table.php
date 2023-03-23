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
        Schema::create('warehouse', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('Site')->default(0);
            $table->string('Name', 100)->index('IDX_Warehouse_Name');
            $table->smallInteger('AllowNegativeBalance')->default(0);
            $table->smallInteger('PermissionProtected')->default(0);
            $table->smallInteger('Trust')->default(0);
            $table->bigInteger('TrustCustomer')->nullable()->index('FK_Warehouse_TrustCustomer');
            $table->bigInteger('TrustCustomerAddress')->nullable()->index('FK_Warehouse_TrustAddress');
            $table->bigInteger('OwnerEmployee')->nullable()->index('FK_Warehouse_Employee');
            $table->bigInteger('OwnerInvestment')->nullable();
            $table->smallInteger('SellBanned')->default(0);
            $table->smallInteger('Foreignn')->default(0);
            $table->string('Zip', 10)->nullable();
            $table->string('City', 100)->nullable();
            $table->string('Street', 100)->nullable();
            $table->string('HouseNumber', 20)->nullable();
            $table->string('ContactName', 100)->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('Fax', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->binary('Comment')->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_Warehouse');
            $table->timestamp('RowModify')->nullable()->index('IRM_Warehouse');
            $table->string('GLN', 40)->nullable();
            $table->smallInteger('IsConsigner')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse');
    }
};
