<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     * 玩家信息表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname')->unique()->comment('昵称');
            $table->string('gender')->comment('性别(0-女,1-男)');
            $table->string('profession')->comment('职业');
            $table->string('real_name')->comment('真实姓名');
            $table->string('identity_id', 18)->unique()->comment('身份证id');
            $table->string('intro')->nullable()->comment('个人简介');
            $table->timestamps();
        });

        DB::update("ALTER TABLE players AUTO_INCREMENT = 10000;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('players');
    }
}
