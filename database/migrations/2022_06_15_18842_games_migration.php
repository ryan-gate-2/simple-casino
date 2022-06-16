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
        Schema::create('gameslist', function (Blueprint $table) {
            $table->id();
            $table->string('game_id')->unique();
            $table->string('game_slug')->unique();
            $table->string('game_name');
            $table->string('game_provider');
            $table->longText('game_desc')->nullable();
            $table->string('extra_id')->nullable();
            $table->integer('demo_available');
            $table->integer('disabled');
            $table->integer('hidden');
            $table->string('index_rating');
            $table->string('api_ext');
            $table->string('type');
            $table->string('parent_id')->nullable();
            $table->string('game_img');
            $table->string('softswiss_id')->nullable();
            $table->string('softswiss_full')->nullable();
            $table->string('softswiss_s1')->nullable();
            $table->string('softswiss_s2')->nullable();
            $table->string('softswiss_s3')->nullable();
            $table->timestamps();
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
    }
};
