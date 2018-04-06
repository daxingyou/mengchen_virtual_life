<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockOrder",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="10022",
 *       ),
 *       @SWG\Property(
 *           property="stock_code",
 *           description="股票代码",
 *           type="string",
 *           example="ABC12345",
 *       ),
 *       @SWG\Property(
 *           property="player_id",
 *           description="下单者玩家id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="direction",
 *           description="下单方向",
 *           type="string",
 *           example="sell",
 *       ),
 *       @SWG\Property(
 *           property="price",
 *           description="下单价格",
 *           type="string",
 *           example="3.00000000",
 *       ),
 *       @SWG\Property(
 *           property="shares",
 *           description="下单数量",
 *           type="string",
 *           example="20.00000000",
 *       ),
 *       @SWG\Property(
 *           property="remained_shares",
 *           description="剩余待成交数量",
 *           type="string",
 *           example="10.00000000",
 *       ),
 *       @SWG\Property(
 *           property="avg_price",
 *           description="平均成交价",
 *           type="string",
 *           example="3.00000000",
 *       ),
 *       @SWG\Property(
 *           property="deal_shares",
 *           description="已成交数量",
 *           type="string",
 *           example="10.00000000",
 *       ),
 *       @SWG\Property(
 *           property="status",
 *           description="订单状态(1-待成交,2-部分成交,3-完全成交,4-已取消)",
 *           type="integer",
 *           example="3",
 *       ),
 *       allOf={
 *           @SWG\Schema(ref="#/definitions/CreatedAtUpdatedAt"),
 *       }
 * )
 *
 */
class StockOrders extends Model
{
    public $timestamps = true;
    protected $table = 'stock_orders';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    protected $casts = [
        'status' => 'integer',
        'player_id' => 'integer',
        //'price' => 'integer',
    ];

    protected $appends = [
        'deal_shares',
    ];

    public function getDealSharesAttribute()
    {
        return sprintf('%.8f', $this->attributes['shares'] - $this->attributes['remained_shares']);
    }

    public function getTotalPriceAttribute()
    {
        return $this->attributes['price'] * $this->attributes['shares'];
    }

    public function updateAvgPrice($dealPrice, $dealShares)
    {
        //avg_price为0则说明此订单是第一次被成交
        if (bccomp($this->avg_price, 0, 8) === 0) {
            $this->avg_price = $dealPrice;
        } else {
            $this->avg_price = (($this->avg_price * $this->deal_shares) + ($dealPrice * $dealShares))
                / ($this->deal_shares + $dealShares);
        }
    }

    //更新状态为完全成交或部分成交
    public function updateStatus()
    {
        //remained > 0
        if ((bccomp($this->remained_shares, 0, 8) === 1)) {
            $this->status = 2;
        } else {
            $this->status = 3;
        }
    }

    //如果使用attribute的话，返回的模型无法使用save保存
    public function getPlayer()
    {
        return Players::where('id', $this->player_id)->first();
    }

    public function getStockHolder()
    {
        $stockHolder = StockHolders::where('stock_code', $this->stock_code)
            ->where('holder_id', $this->player_id)
            ->first();
        //没有持股时买入，如果返回空trading报错
        if (empty($stockHolder)) {
            $stockHolder = StockHolders::create([
                'stock_code' => $this->stock_code,
                'holder_id' => $this->player_id,
                'total_shares' => 0,
                'frozen_shares' => 0,
            ]);
        }
        return $stockHolder;
    }
}
