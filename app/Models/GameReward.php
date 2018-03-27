<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameReward extends Model
{
    public $timestamps = false;
    protected $table = 'game_reward';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
