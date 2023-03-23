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
        Schema::create('customerorderstatus', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->unique('IDX_CustomerOrderStatus_N');
            $table->integer('ForeColor')->nullable();
            $table->integer('BackColor')->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->unique('IRC_CustomerOrderStatus');
            $table->timestamp('RowModify')->nullable()->unique('IRM_CustomerOrderStatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customerorderstatus');
    }
};
