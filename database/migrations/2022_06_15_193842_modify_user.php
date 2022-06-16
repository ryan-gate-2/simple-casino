<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::table('users', function ($table) {
            $table->string('active_currency', 100)->after('password')->default('USD');
            $table->string('balance_usd', 100)->after('password')->default('0');
            $table->string('balance_eur', 100)->after('password')->default('0');
            $table->string('balance_cad', 100)->after('password')->default('0');
            $table->string('games_played', 100)->after('password')->default('0');
            $table->string('player_id', 100)->after('id');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
    
};