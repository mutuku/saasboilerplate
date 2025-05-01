<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('priority')->nullable();
            $table->string('iso_code')->nullable();
            $table->string('name')->nullable();
            $table->string('symbol')->nullable();
            $table->string('subunit')->nullable();
            $table->integer('subunit_to_unit')->nullable();
            $table->string('symbol_first')->nullable();
            $table->string('html_entity')->nullable();
            $table->string('decimal_mark')->nullable();
            $table->string('thousands_separator')->nullable();
            $table->integer('iso_numeric')->nullable();
            $table->timestamps();
            $table->softDeletesTz();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
