<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsSold extends Model
{
    public $timestamps = false;
    protected $table = 'points_sold';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $casts = [
        //'sold_points' => 'float',
    ];
}
