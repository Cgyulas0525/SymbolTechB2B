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
        Schema::create('transportmodelang', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->integer('Lang')->default(0);
            $table->bigInteger('TransportMode')->index('FK_TransportModeLang_TM');
            $table->string('Name', 100);
            $table->binary('VoucherComment')->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_TransportModeLang');
            $table->timestamp('RowModify')->nullable()->index('IRM_TransportModeLang');

            $table->index(['Lang', 'TransportMode'], 'IDX_TransportModeLang_LTM');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transportmodelang');
    }
};
