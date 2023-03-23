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
        Schema::create('customercontactfavoriteproduct', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('customercontact_id');
            $table->integer('product_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customercontact_id', 'product_id'], 'ccfp_cc_id_p_id_index');
            $table->index(['product_id', 'customercontact_id'], 'ccfp_p_id_cct_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customercontactfavoriteproduct');
    }
};
