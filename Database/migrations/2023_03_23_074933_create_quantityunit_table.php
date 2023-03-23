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
        Schema::create('quantityunit', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 10)->index('IDX_QuantityUnit_Name');
            $table->integer('CashRegIndex')->default(0);
            $table->integer('QuantityDigits')->default(0);
            $table->smallInteger('Standard')->default(0);
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_QuantityUnit');
            $table->timestamp('RowModify')->nullable()->index('IRM_QuantityUnit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quantityunit');
    }
};
