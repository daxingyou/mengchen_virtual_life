<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_code', 8)->comment('股票代码');
            $table->unsignedInteger('player_id')->comment('下单者玩家id');
            $table->string('direction')->comment('下单方向');
            $table->decimal('price', 18, 8)->comment('下单价');
            $table->decimal('shares', 18, 8)->comment('下单数量');
            $table->decimal('remained_shares', 18, 8)->comment('剩余待成交股数');
            $table->decimal('avg_price', 18, 8)->comment('平均成交价');
            $table->unsignedInteger('status')->comment('状态(1-待成交,2-部分成交,3-完全成交,4-已取消)');
            $table->timestamps();
        });

        DB::update("ALTER TABLE stock_orders AUTO_INCREMENT = 10000;");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_orders');
    }
}
