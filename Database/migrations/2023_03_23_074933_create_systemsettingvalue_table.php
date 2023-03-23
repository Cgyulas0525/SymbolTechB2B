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
        Schema::create('systemsettingvalue', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->unique('IDX_SystemSettingValue_N');
            $table->integer('ValueType')->default(0);
            $table->smallInteger('ValueBool')->nullable();
            $table->integer('ValueInt')->nullable();
            $table->decimal('ValueDecimal', 18, 4)->nullable();
            $table->timestamp('ValueDate')->nullable();
            $table->bigInteger('ValueBigInt')->nullable();
            $table->string('ValueString', 100)->nullable();
            $table->binary('ValueText')->nullable();
            $table->binary('ValueBinary')->nullable();
            $table->timestamp('RowCreate')->nullable();
            $table->timestamp('RowModify')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('systemsettingvalue');
    }
};
