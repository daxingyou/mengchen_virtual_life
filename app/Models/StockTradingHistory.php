<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
