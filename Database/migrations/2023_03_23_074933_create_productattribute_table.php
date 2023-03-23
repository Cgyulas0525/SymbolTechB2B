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
        Schema::create('productattribute', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_ProductAttribute_Name');
            $table->integer('AttributeTypeValue')->default(0);
            $table->string('Postfix', 8)->nullable();
            $table->smallInteger('Filter')->default(0);
            $table->smallInteger('HideFromVoucher')->default(0);
            $table->integer('Priority')->default(0);
            $table->smallInteger('HideFromWeb')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductAttribute');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductAttribute');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productattribute');
    }
};
