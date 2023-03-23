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
        Schema::create('systemsetting', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->binary('ProductKey')->nullable();
            $table->binary('Company')->nullable();
            $table->binary('Setting')->nullable();
            $table->timestamp('RowVersion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('systemsetting');
    }
};
