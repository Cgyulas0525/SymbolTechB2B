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
        Schema::create('excelimport', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('Field0')->nullable();
            $table->string('Field1')->nullable();
            $table->string('Field2')->nullable();
            $table->string('Field3')->nullable();
            $table->string('Field4')->nullable();
            $table->string('Field5')->nullable();
            $table->string('Field6')->nullable();
            $table->string('Field7')->nullable();
            $table->string('Field8')->nullable();
            $table->string('Field9')->nullable();
            $table->string('Field10')->nullable();
            $table->string('Field11')->nullable();
            $table->string('Field12')->nullable();
            $table->string('Field13')->nullable();
            $table->string('Field14')->nullable();
            $table->string('Field15')->nullable();
            $table->string('Field16')->nullable();
            $table->string('Field17')->nullable();
            $table->string('Field18')->nullable();
            $table->string('Field19')->nullable();
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('excelimport');
    }
};
