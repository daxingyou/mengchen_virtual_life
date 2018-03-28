<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDividendPolicy extends Model
{
    public $timestamps = true;
    protected $table = 'stock_dividend_policy';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
