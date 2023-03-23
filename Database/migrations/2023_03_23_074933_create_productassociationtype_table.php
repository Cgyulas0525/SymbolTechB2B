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
        Schema::create('productassociationtype', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->string('Name', 100)->index('IDX_ProductAssociationType_Name');
            $table->smallInteger('Deleted')->default(0);
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductAssociationType');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductAssociationType');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productassociationtype');
    }
};
