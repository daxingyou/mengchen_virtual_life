<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHolders extends Model
{
    public $timestamps = true;
    protected $table = 'stock_holders';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        'id'
    ];

    protected $casts = [
        'total_shares' => 'float',
        'frozen_shares' => 'float',
    ];
}
