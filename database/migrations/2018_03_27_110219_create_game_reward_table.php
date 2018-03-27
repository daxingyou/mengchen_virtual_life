<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameRewardTable extends Migration
{
    /**
     * Run the migrations.
     * 游戏奖励日志表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_reward', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id')->comment('玩家id');
            $table->string('action')->comment('游戏行为');
            $table->double('reward')->comment('赚取身价数');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_reward');
    }
}
