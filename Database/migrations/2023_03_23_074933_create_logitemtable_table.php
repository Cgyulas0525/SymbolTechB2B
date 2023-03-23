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
        Schema::create('logitemtable', function (Blueprint $table) {
            $table->integer('id', true)->unique('logitemtable_id_uindex');
            $table->integer('logitem_id')->index('logitemtable__logitem_index');
            $table->string('tablename', 100);
            $table->integer('recordid')->nullable();
            $table->integer('targetrecordid')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logitemtable');
    }
};
