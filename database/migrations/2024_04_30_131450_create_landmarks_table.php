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
            $table->string('sunday');
            $table->string('monday');
            $table->string('tuesday');
            $table->string('wednesday');
            $table->string('thursday');
            $table->string('friday');
            $table->string('saturday');
            $table->integer('egyptian_ticket');
            $table->integer('egyptian_student_ticket');
            $table->integer('foreign_ticket');
            $table->integer('foreign_student_ticket');
            $table->string('booking');  
            $table->string('region'); 
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
