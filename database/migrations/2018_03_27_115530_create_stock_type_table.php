<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTypeTable extends Migration
{
    protected $defaultTypes = [
        [
            'type_name' => '音乐',
        ],
        [
            'type_name' => '旅行',
        ],
        [
            'type_name' => '教育',
        ]
    ];

    /**
     * Run the migrations.
     * 股票类型表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type_name')->comment('类型名称');
            $table->unsignedInteger('creator_id')->nullable()->comment('自定义类型创建者玩家id');
            $table->timestamps();
        });

        DB::table('stock_type')->insert($this->defaultTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_type');
    }
}
