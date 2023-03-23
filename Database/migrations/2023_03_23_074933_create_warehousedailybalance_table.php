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
        Schema::create('warehousedailybalance', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('Product');
            $table->bigInteger('Warehouse')->index('FK_WarehouseDailyBalance_Wareh');
            $table->timestamp('Date')->nullable()->useCurrent();
            $table->decimal('Balance', 18, 4)->default(0)->index('IDX_WarehouseDailyBalance_Blc');
            $table->timestamp('RowCreate')->nullable()->index('IRC_WarehouseDailyBalance');
            $table->timestamp('RowModify')->nullable()->index('IRM_WarehouseDailyBalance');

            $table->index(['Product', 'Warehouse', 'Date'], 'UNQ_WarehouseDailyBalance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehousedailybalance');
    }
};
