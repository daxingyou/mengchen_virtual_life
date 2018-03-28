<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    public $timestamps = true;
    protected $table = 'stock_type';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
