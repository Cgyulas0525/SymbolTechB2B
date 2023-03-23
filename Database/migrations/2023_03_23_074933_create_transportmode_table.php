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
        Schema::create('transportmode', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_TransportMode_Name');
            $table->decimal('DiscountPercent', 18, 4)->default(0);
            $table->string('VoucherComment', 100)->nullable();
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_TransportMode');
            $table->timestamp('RowModify')->nullable()->index('IRM_TransportMode');
            $table->string('Code', 40)->nullable();
            $table->smallInteger('Personal')->nullable();
            $table->smallInteger('OwnDelivery')->nullable();
            $table->bigInteger('ParcelCompany')->nullable();

            $table->index(['Deleted', 'Id'], 'IDX_TransportMode_Delete');
            $table->index(['Id'], 'transportmode_Id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transportmode');
    }
};
