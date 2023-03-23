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
        Schema::create('logitemtabledetail', function (Blueprint $table) {
            $table->integer('id', true)->unique('logitemtabledetail_id_uindex');
            $table->integer('logitemtable_id');
            $table->string('changedfield', 100);
            $table->integer('oldinteger')->nullable();
            $table->string('oldstring', 250)->nullable();
            $table->dateTime('olddatetime')->nullable();
            $table->decimal('olddecimal', 18, 4)->nullable();
            $table->integer('newinteger')->nullable();
            $table->string('newstring', 250)->nullable();
            $table->dateTime('newdatetime')->nullable();
            $table->decimal('newdecimal', 18, 4)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['logitemtable_id', 'changedfield'], 'logitemtabledetail__litid_index');
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
        Schema::dropIfExists('logitemtabledetail');
    }
};
