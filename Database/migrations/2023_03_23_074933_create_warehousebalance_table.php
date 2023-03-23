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
        Schema::create('warehousebalance', function (Blueprint $table) {
            $table->bigInteger('Id', true);
            $table->bigInteger('Product');
            $table->bigInteger('Warehouse')->index('FK_WarehouseBalance_Warehouse');
            $table->decimal('Balance', 18, 4)->default(0)->index('IDX_WarehouseBalance_Balance');
            $table->decimal('AllocatedBalance', 18, 4)->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_WarehouseBalance');
            $table->timestamp('RowModify')->nullable()->index('IRM_WarehouseBalance');

            $table->index(['Product', 'Warehouse'], 'UNQ_WarehouseBalance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehousebalance');
    }
};
