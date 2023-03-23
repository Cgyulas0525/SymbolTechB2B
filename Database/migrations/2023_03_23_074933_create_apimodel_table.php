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
        Schema::create('apimodel', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('api_id');
            $table->string('model', 100);
            $table->integer('recordnumber')->default(0);
            $table->integer('insertednumber')->default(0);
            $table->integer('updatednumber')->default(0);
            $table->integer('errornumber')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['api_id', 'id'], 'apimodel_api_id_id_uindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apimodel');
    }
};
