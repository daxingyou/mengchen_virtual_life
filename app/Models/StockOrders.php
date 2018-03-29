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
}
