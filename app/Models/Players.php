<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    public $timestamps = true;
    protected $table = 'players';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $appends = [
        //
    ];

    protected $casts = [
        'game_points' => 'float',
        'points' => 'float',
    ];

    public function getPlayerPetAttribute()
    {
        $playerPet = PlayerPet::where('player_id', $this->attributes['id'])->first();
        if (empty($playerPet)) {
            return PlayerPet::create([
                'player_id' => $this->attributes['id'],
                'pet_name' => 'pet',
                'pet_exp' => 0,
            ]);
        } else {
            return $playerPet;
        }
    }

    public function getStockIpoAttribute()
    {
        return StockIpo::where('issuer_id', $this->attributes['id'])->first();
    }

    public function getStocksAttribute()
    {
        return StockHolders::where('holder_id', $this->attributes['id'])->get();
    }
}
