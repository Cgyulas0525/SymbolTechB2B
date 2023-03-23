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
        Schema::create('currencyrate', function (Blueprint $table) {
            $table->bigInteger('Id', true);
            $table->bigInteger('Currency');
            $table->timestamp('ValidFrom')->useCurrent();
            $table->decimal('Rate', 18, 4)->default(0);
            $table->decimal('RateBuy', 18, 4)->nullable();
            $table->decimal('RateSell', 18, 4)->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_CurrencyRate');
            $table->timestamp('RowModify')->nullable()->index('IRM_CurrencyRate');

            $table->unique(['Currency', 'ValidFrom'], 'UNQ_CurrencyRate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencyrate');
    }
};
