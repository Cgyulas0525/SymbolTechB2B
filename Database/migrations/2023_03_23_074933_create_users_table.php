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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('email', 191)->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->integer('employee_id')->nullable();
            $table->integer('customercontact_id')->nullable();
            $table->integer('rendszergazda')->nullable()->default(0);
            $table->text('megjegyzes')->nullable();
            $table->bigInteger('CustomerAddress')->nullable();
            $table->bigInteger('TransportMode')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->rememberToken();
            $table->string('image_url', 191)->nullable();

            $table->unique(['customercontact_id', 'deleted_at'], 'users_costumercontact_id_uindex');
            $table->unique(['employee_id', 'deleted_at'], 'users_employee_id_deleted_at_uindex');
            $table->unique(['id'], 'users_id_uindex');
            $table->unique(['rendszergazda', 'id'], 'users_rendszergazda_id_uindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
