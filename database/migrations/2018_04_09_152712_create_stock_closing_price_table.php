<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockClosingPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_closing_price', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_code', 8)->comment('股票代码');
            $table->decimal('closing_price', 18, 8)->comment('当日收盘价');
            $table->date('date')->comment('日期');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_closing_price');
    }
}
