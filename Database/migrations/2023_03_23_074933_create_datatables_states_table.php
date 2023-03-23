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
        Schema::create('datatables_states', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->string('name', 20);
            $table->text('state');
            $table->text('array')->nullable();

            $table->index(['name', 'user_id'], 'ds_name_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datatables_states');
    }
};
