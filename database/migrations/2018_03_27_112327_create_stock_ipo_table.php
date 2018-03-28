<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockIpoTable extends Migration
{
    /**
     * Run the migrations.
     * 股票ipo记录表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_ipo', function (Blueprint $table) {
            $table->increments('id');
            //未加唯一键，因为可能此股票代码发行失败，那么此代码可以被其他人ipo使用
            $table->string('stock_code', 8)->comment('股票代码');
            $table->unsignedInteger('stock_type_id')->comment('股票类型id');
            $table->unsignedInteger('issuer_id')->comment('股票发行人id');
            $table->decimal('ipo_price', 18, 8)->comment('发行价');
            $table->decimal('ipo_shares', 18, 8)->comment('发行数量');
            $table->decimal('ipo_remained_shares', 18, 8)->comment('剩余待认购数量');
            $table->unsignedInteger('dividend_policy_id')->comment('分红方案id');
            $table->string('intro')->nullable()->comment('简介');
            $table->unsignedInteger('status')->comment('状态(1-发行成功)');
            $table->timestamps();

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
        Schema::dropIfExists('stock_ipo');
    }
}
