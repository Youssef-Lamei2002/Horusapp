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
        Schema::create('landmark__imgs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('img');
            $table->unsignedBigInteger('landmark_id');
            $table->foreign('landmark_id')->references('id')->on('landmarks');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landmark__imgs');
    }
};
