<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIpoSbuscription extends Model
{
    public $timestamps = false;
    protected $table = 'stock_ipo_subscription';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    protected $casts = [
        'shares_subscribed' => 'float',
    ];
}
