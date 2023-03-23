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
        Schema::create('productassociation', function (Blueprint $table) {
            $table->bigInteger('Id')->primary();
            $table->bigInteger('OriginalProduct')->index('FK_ProductAssociation_Original');
            $table->bigInteger('AssociatedProduct')->index('FK_ProductAssociation_Associate');
            $table->bigInteger('ProductAssociationType')->index('FK_ProductAssociation_AssocType');
            $table->timestamp('RowCreate')->nullable()->index('IRC_ProductAssociation');
            $table->timestamp('RowModify')->nullable()->index('IRM_ProductAssociation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productassociation');
    }
};
