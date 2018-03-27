<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationTable extends Migration
{
    protected $defaultConfiguration = [
        'max_friends' => 500,
        'max_holding_stocks' => 500,
        'base_ipo_shares' => 100,
        'player_id' => 0,
    ];

    /**
     * Run the migrations.
     * 配置表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('max_friends')->default(500)->comment('最大好友数');
            $table->unsignedInteger('max_holding_stocks')->default(500)->comment('最大持股数');
            $table->decimal('base_ipo_shares', 18, 8)->default(100)->comment('ipo股票发行基数');
            $table->unsignedInteger('player_id')->default(0)->comment('玩家id,0为通用配置');
            $table->timestamps();
        });

        DB::table('configuration')->insert($this->defaultConfiguration);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration');
    }
}
