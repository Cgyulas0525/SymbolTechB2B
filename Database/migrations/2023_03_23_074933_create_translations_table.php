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
        Schema::create('translations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('huname', 500);
            $table->char('language', 2);
            $table->string('name', 500);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['huname', 'language'], 'translation_hu_lang');
            $table->unique(['language', 'name'], 'translation_lang_name');
            $table->index(['language', 'huname']);
            $table->index(['name', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
};
