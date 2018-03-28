<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockIpoSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     * ipo股票认购记录表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_ipo_subscription', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stock_code', 8)->comment('股票代码');
            $table->unsignedInteger('subscriber')->comment('认购者玩家id');
            $table->decimal('shares_subscribed', 18, 8)->comment('认购数量');
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
        Schema::dropIfExists('stock_ipo_subscription');
    }
}
