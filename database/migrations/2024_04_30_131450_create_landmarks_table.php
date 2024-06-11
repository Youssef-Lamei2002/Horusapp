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
        Schema::create('landmarks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('description');
            $table->float('rating');
            $table->string('location');
            $table->string('tourism_type');
            $table->time('sunday_open')->nullable();
            $table->time('sunday_close')->nullable();
            $table->time('monday_open')->nullable();
            $table->time('monday_close')->nullable();
            $table->time('tuesday_open')->nullable();
            $table->time('tuesday_close')->nullable();
            $table->time('wednesday_open')->nullable();
            $table->time('wednesday_close')->nullable();
            $table->time('thursday_open')->nullable();
            $table->time('thursday_close')->nullable();
            $table->time('friday_open')->nullable();
            $table->time('friday_close')->nullable();
            $table->time('saturday_open')->nullable();
            $table->time('saturday_close')->nullable();
            $table->integer('egyptian_ticket');
            $table->integer('egyptian_student_ticket');
            $table->integer('foreign_ticket');
            $table->integer('foreign_student_ticket');
            $table->string('booking');  
            $table->string('region'); 
            $table->boolean('needTourguide');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landmarks');
    }
};
