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
        Schema::create('quantityunitlang', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->integer('Lang')->default(0);
            $table->bigInteger('QuantityUnit')->index('FK_QuantityUnitLang_QU');
            $table->string('Name', 100);
            $table->timestamp('RowCreate')->nullable()->index('IRC_QuantityUnitLang');
            $table->timestamp('RowModify')->nullable()->index('IRM_QuantityUnitLang');

            $table->index(['Lang', 'QuantityUnit'], 'IDX_QuantityUnitLang_LQU');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quantityunitlang');
    }
};
