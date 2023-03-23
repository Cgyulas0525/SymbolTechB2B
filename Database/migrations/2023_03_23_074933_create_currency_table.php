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
        Schema::create('currency', function (Blueprint $table) {
            $table->bigInteger('Id')->index('currency_Id_index');
            $table->string('Name', 8)->unique('UNQ_Currency_Name');
            $table->string('Sign', 4);
            $table->integer('RoundDigits')->default(0);
            $table->integer('DetailRoundDigits')->default(0);
            $table->smallInteger('GrossRound')->default(0);
            $table->binary('Denomination')->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_Currency');
            $table->timestamp('RowModify')->nullable()->index('IRM_Currency');

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
        Schema::dropIfExists('currency');
    }
};
