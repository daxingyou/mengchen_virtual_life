<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerPetTable extends Migration
{
    /**
     * Run the migrations.
     * 玩家宠物表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_pet', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('player_id')->comment('玩家id');
            $table->string('pet_name')->comment('宠物名字');
            $table->double('pet_exp')->comment('宠物经验');
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
        Schema::dropIfExists('player_pet');
    }
}
