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
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id')->unique();
            $table->string('player_id');
            $table->string('credit');
            $table->string('debit');
            $table->string('old_balance');
            $table->string('new_balance');
            $table->string('currency');
            $table->string('extra_data')->nullable();
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
