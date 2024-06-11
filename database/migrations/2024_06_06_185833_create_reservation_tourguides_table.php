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
        Schema::create('reservation_tourguides', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('tourist_id');
            $table->foreign('tourist_id')->references('id')->on('tourists');
            $table->unsignedBigInteger('tourguide_id');
            $table->foreign('tourguide_id')->references('id')->on('tourguides');
            $table->unsignedBigInteger('landmark_id');
            $table->foreign('landmark_id')->references('id')->on('landmarks');
            $table->float('hours');
            $table->float('price_of_hour');
            $table->boolean('isAccepted');
            $table->boolean('isFinished');
            $table->time('starting_time');
            $table->time('finished_time');
            $table->date('day');
        });
    }  
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_tourguides');
    }
};
