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
        Schema::create('tourguides', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('email')->email()->unique();
            $table->string('password');
            $table->boolean('gender')->comment('1:male,0:female');
            $table->string('nationality');
            $table->string('phone_number');
            $table->string('profile_pic');
            $table->string('ssn');
            $table->boolean('email_type')->default(1);
            $table->boolean('isBlocked')->default(0);
            $table->boolean('isApproved')->default(0);
            $table->float('rate')->nullable();
            $table->float('price');
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
        Schema::dropIfExists('tourguides');
    }
};
