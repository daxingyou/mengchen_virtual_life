<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockHoldersTable extends Migration
{
    /**
     * Run the migrations
     * 玩家持股表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_holders', function (Blueprint $table) {
            $table->string('stock_code', 8)->comment('股票代码');
            $table->unsignedInteger('holder_id')->comment('持股人玩家id');
            $table->double('total_shares')->comment('持股数');
            $table->double('frozen_shares')->comment('冻结股数');
            $table->timestamps();

            $table->index('stock_code');
            $table->index('holder_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_holders');
    }
}
