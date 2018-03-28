<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIpo extends Model
{
    public $timestamps = true;
    protected $table = 'stock_ipo';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $casts = [
        'ipo_price' => 'float',
        'ipo_shares' => 'float',
        'ipo_remained_shares' => 'float',
    ];
}
