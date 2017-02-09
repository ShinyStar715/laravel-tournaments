<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTeamTable extends Migration
{

    public function up()
    {
        Schema::create('team', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('championship_id')->unsigned()->index(); // A checar
            $table->string('picture')->nullable();
            $table->string('entity_type')->nullable(); // Club, Assoc, Fed
            $table->integer('entity_id')->unsigned()->nullable()->index();
            $table->timestamps();


            $table->foreign('championship_id')
                ->references('id')
                ->on('championship')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['championship_id', 'name']);
        });
    }

    public function down()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('team');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}