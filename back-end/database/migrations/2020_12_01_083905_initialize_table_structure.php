<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitializeTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'player', 
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->double('balance')->default(1000);
                $table->timestamps();
                $table->softDeletes();
            }
        );
        Schema::create(
            'bet', 
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->double('stake_amount');
                $table->unsignedBigInteger('player_id');
                $table->boolean('ended')->default(false);
                $table->foreign('player_id')->references('id')->on('player');
                $table->timestamps();
            }
        );
        Schema::create(
            'balance_transaction', 
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('player_id');
                $table->unsignedBigInteger('bet_id');
                $table->double('amount');
                $table->double('amount_before');
                $table->foreign('player_id')->references('id')->on('player');
                $table->foreign('bet_id')->references('id')->on('bet');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['player_id']);
            }
        );
        Schema::create(
            'bet_selections', 
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('bet_id');
                $table->integer('selection_id');
                $table->double('odds');
                $table->timestamps();
            }
        );
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
}
