<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockAskingTable extends Migration
{
    /**
     * Run the migrations.
     * 卖单订单表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_asking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_code', 8)->comment('股票代码');
            $table->unsignedInteger('player_id')->comment('下单者玩家id');
            $table->double('price')->comment('下单价');
            $table->double('shares')->comment('下单数量');
            $table->double('avg_price')->comment('平均成交价');
            $table->double('remained_shares')->comment('剩余待成交股数');
            $table->unsignedInteger('status')->comment('状态(1-待成交,2-部分成交,3-完全成交,4-已取消)');
            $table->timestamps();

            DB::update("ALTER TABLE players AUTO_INCREMENT = 100000;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_asking');
    }
}
