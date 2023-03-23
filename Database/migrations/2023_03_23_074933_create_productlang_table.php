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
        Schema::create('productlang', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->integer('Lang')->default(0);
            $table->bigInteger('Product')->index('FK_ProductLang_Product');
            $table->string('Name', 100);
            $table->binary('Comment')->nullable();
            $table->string('WebName', 100)->nullable();
            $table->binary('WebDescription')->nullable();
            $table->string('WebUrl', 100)->nullable();
            $table->binary('WebMetaDescription')->nullable();
            $table->string('WebKeywords', 100)->nullable();
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductLang');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductLang');

            $table->index(['Lang', 'Product'], 'IDX_ProductLang_PL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productlang');
    }
};
