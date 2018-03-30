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
}
