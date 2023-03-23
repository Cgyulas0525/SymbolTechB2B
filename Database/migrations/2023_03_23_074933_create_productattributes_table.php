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
        Schema::create('productattributes', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('Product')->index('FK_ProductAttributes_Product');
            $table->bigInteger('ProductAttribute')->index('FK_ProductAttributes_ProdAttr');
            $table->string('ValueString', 100)->index('IDX_ProductAttributes_VStr');
            $table->decimal('ValueDecimal', 18, 4)->default(0)->index('IDX_ProductAttributes_VDec');
            $table->timestamp('ValueDate')->nullable()->useCurrent()->index('IDX_ProductAttributes_VDat2');
            $table->smallInteger('ValueBool')->default(0);
            $table->bigInteger('ValueLookup')->nullable()->index('FK_ProductAttributes_PAValue');
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductAttributes');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductAttributes');

            $table->index(['ValueDate'], 'IDX_ProductAttributes_VDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productattributes');
    }
};
