<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $appends = [
        'deal_shares',
    ];

    public function getDealSharesAttribute()
    {
        return $this->attributes['shares'] - $this->attributes['remained_shares'];
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
        return StockHolders::where('stock_code', $this->stock_code)
            ->where('holder_id', $this->player_id)
            ->first();
    }
}
