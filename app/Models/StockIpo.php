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

    protected $appends = [
        'ipo_subscribed_shares'
    ];

    protected $casts = [
        //'ipo_price' => 'float',
        //'ipo_shares' => 'float',
        //'ipo_remained_shares' => 'float',
    ];

    public function getIpoSubscribedSharesAttribute()
    {
        return $this->attributes['ipo_shares'] - $this->attributes['ipo_remained_shares'];
    }
}
