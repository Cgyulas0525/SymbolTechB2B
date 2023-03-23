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
        Schema::create('vat', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->smallInteger('DirectionBuy')->default(0);
            $table->string('Name', 100);
            $table->decimal('Rate', 18, 4)->default(0);
            $table->decimal('ExpenseRate', 18, 4)->nullable();
            $table->smallInteger('Converse')->default(0);
            $table->string('ConverseText', 100)->nullable();
            $table->smallInteger('Eu')->default(0);
            $table->integer('CashRegIndex')->default(0);
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_Vat');
            $table->timestamp('RowModify')->nullable()->index('IRM_Vat');
            $table->string('Description', 100)->nullable();
            $table->smallInteger('ShowDetailName')->default(0);

            $table->unique(['DirectionBuy', 'Name', 'Rate'], 'UNQ_Vat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vat');
    }
};
