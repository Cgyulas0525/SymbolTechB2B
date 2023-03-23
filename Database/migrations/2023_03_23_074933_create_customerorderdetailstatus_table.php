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
        Schema::create('customerorderdetailstatus', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_CustomerOrderDetailStatus_N');
            $table->smallInteger('StrictAllocate')->default(0);
            $table->smallInteger('Deleted')->default(0);
            $table->integer('EditMode')->default(0);
            $table->integer('ForeColor')->nullable();
            $table->integer('BackColor')->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_CustomerOrderDetailStatus');
            $table->timestamp('RowModify')->nullable()->index('IRM_CustomerOrderDetailStatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customerorderdetailstatus');
    }
};
