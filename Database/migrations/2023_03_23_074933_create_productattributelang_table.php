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
        Schema::create('productattributelang', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->integer('Lang')->default(0);
            $table->bigInteger('ProductAttribute')->index('FK_ProductAttributeLang_PAttr');
            $table->string('Name', 100)->nullable();
            $table->string('Postfix', 8)->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductAttributeLang');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductAttributeLang');

            $table->index(['Lang', 'ProductAttribute'], 'IDX_ProductAttributeLang_LP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productattributelang');
    }
};
