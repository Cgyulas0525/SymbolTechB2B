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
        Schema::create('pricecategory', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_PriceCategory_Name');
            $table->smallInteger('IncomingPrice')->default(0);
            $table->smallInteger('BasePrice')->default(0);
            $table->smallInteger('RateRelativeToBasePrice')->default(0);
            $table->decimal('Rate', 18, 4)->nullable();
            $table->smallInteger('NineRule')->default(0);
            $table->smallInteger('DisableAutoCalc')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_PriceCategory');
            $table->timestamp('RowModify')->nullable()->index('IRM_PriceCategory');
            $table->smallInteger('GrossPrices')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricecategory');
    }
};
