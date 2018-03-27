<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockDividendPolicyTable extends Migration
{
    protected $defaultPolicy = [
        'policy' => '定期身价分红',
    ];

    /**
     * Run the migrations.
     * 股票分红方案记录表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_dividend_policy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('policy')->comment('分红方案');
            $table->unsignedInteger('player_id')->nullable()->comment('自定义分红方案玩家id');
            $table->timestamps();
        });

        DB::table('stock_dividend_policy')->insert($this->defaultPolicy);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_dividend_policy');
    }
}
