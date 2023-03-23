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
        Schema::create('apimodelerror', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('apimodel_id');
            $table->string('smtp', 2000);
            $table->string('error', 2000);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['apimodel_id', 'id'], 'apimodelerror_apimodel_id_id_uindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apimodelerror');
    }
};
