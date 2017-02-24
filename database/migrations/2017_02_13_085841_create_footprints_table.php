<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFootprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('footprints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('goal_id');
            $table->integer('index_number');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('goal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('footprints');
    }
}
