<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTradingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     * 股票交易记录
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_trading_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_code', 8)->comment('股票代码');
            $table->decimal('price', 18, 8)->comment('成交价');
            $table->decimal('shares', 18, 8)->comment('成交数量');
            $table->string('taker_direction')->comment('吃单方向');
            $table->unsignedInteger('maker_order_id')->comment('maker订单号');
            $table->unsignedInteger('taker_order_id')->comment('taker订单号');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index('stock_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_trading_history');
    }
}
