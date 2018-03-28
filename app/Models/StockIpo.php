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
}
