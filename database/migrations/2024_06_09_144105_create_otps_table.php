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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('tourist_id')->nullable();
            $table->unsignedBigInteger('tourguide_id')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreign('tourist_id')->references('id')->on('tourists');
            $table->foreign('tourguide_id')->references('id')->on('tourguides');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
};
