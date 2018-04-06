<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockTradingHistory",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="stock_code",
 *           description="股票代码",
 *           type="string",
 *           example="ABC12345",
 *       ),
 *       @SWG\Property(
 *           property="price",
 *           description="成交价格",
 *           type="string",
 *           example="3.00000000",
 *       ),
 *       @SWG\Property(
 *           property="shares",
 *           description="成交数量",
 *           type="string",
 *           example="20.00000000",
 *       ),
 *       @SWG\Property(
 *           property="taker_direction",
 *           description="主动成交方向",
 *           type="string",
 *           example="sell",
 *       ),
 *       @SWG\Property(
 *           property="maker_order_id",
 *           description="做市商订单号",
 *           type="integer",
 *           format="int32",
 *           example="10001",
 *       ),
 *       @SWG\Property(
 *           property="taker_order_id",
 *           description="主动成交订单号",
 *           type="integer",
 *           format="int32",
 *           example="10002",
 *       ),
 *       @SWG\Property(
 *           property="created_at",
 *           description="创建时间",
 *           type="string",
 *           example="2018-03-30 16:03:14",
 *       ),
 * )
 *
 */
class StockTradingHistory extends Model
{
    public $timestamps = false;
    protected $table = 'stock_trading_history';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    protected $appends = [
        //
    ];

    public function makerOrder()
    {
        return $this->hasOne('App\Models\StockOrders', 'id', 'maker_order_id');
    }

    public function takerOrder()
    {
        return $this->hasOne('App\Models\StockOrders', 'id', 'taker_order_id');
    }

    public function getOwnerAttribute()
    {
        $stockIpo = StockIpo::where('stock_code', $this->attributes['stock_code'])->firstOrFail();
        return Players::where('id', $stockIpo->issuer_id)->firstOrFail()
            ->setHidden(['created_at', 'updated_at']);
    }
}
