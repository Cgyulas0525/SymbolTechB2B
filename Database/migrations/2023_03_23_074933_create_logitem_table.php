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
        Schema::create('logitem', function (Blueprint $table) {
            $table->integer('id', true)->unique('logitem_id_uindex');
            $table->integer('customer_id');
            $table->integer('user_id');
            $table->integer('eventtype');
            $table->timestamp('eventdatetime')->index('logitem__eventdatetime_index');
            $table->string('remoteaddress', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'user_id', 'eventdatetime'], 'logitem__customeruser_index');
            $table->index(['user_id', 'eventdatetime'], 'logitem__user_index');
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
        Schema::dropIfExists('logitem');
    }
};
