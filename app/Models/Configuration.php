<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    public $timestamps = true;
    protected $table = 'configuration';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $casts = [
        'max_friends' => 'integer',
        'max_holding_stocks' => 'integer',
        'base_ipo_shares' => 'float',
        'point_price' => 'float',
        'pet_reward' => 'float',
        'pet_exp' => 'pet_exp',
        'player_id' => 'integer',
    ];
}
