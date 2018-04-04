<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="Player",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="open_id",
 *           description="玩家微信openid",
 *           type="string",
 *           example="odh7zsgI75iT8FRh0fGlSojc9PWM",
 *       ),
 *       @SWG\Property(
 *           property="nickname",
 *           description="玩家昵称",
 *           type="string",
 *           example="小明",
 *       ),
 *       @SWG\Property(
 *           property="gender",
 *           description="玩家性别（0-女,1-男）",
 *           type="integer",
 *           format="int32",
 *           example="0",
 *       ),
 *       @SWG\Property(
 *           property="profession",
 *           description="玩家职业",
 *           type="string",
 *           example="律师",
 *       ),
 *       @SWG\Property(
 *           property="real_name",
 *           description="玩家真实姓名",
 *           type="string",
 *           example="张飞",
 *       ),
 *       @SWG\Property(
 *           property="identity_id",
 *           description="玩家身份证id",
 *           type="string",
 *           example="456998188702118765",
 *       ),
 *       @SWG\Property(
 *           property="intro",
 *           description="个人简介",
 *           type="string",
 *       ),
 *       @SWG\Property(
 *           property="game_points",
 *           description="宠物交互赚取游戏身价数",
 *           type="string",
 *           example="0.10000000",
 *       ),
 *       @SWG\Property(
 *           property="points",
 *           description="个人流通身价总数（包括冻结）",
 *           type="string",
 *           example="374.00000000",
 *       ),
 *       @SWG\Property(
 *           property="frozen_points",
 *           description="冻结身价数",
 *           type="string",
 *           example="4.00000000",
 *       ),
 *       allOf={
 *           @SWG\Schema(ref="#/definitions/CreatedAtUpdatedAt"),
 *       }
 * )
 *
 */
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
        'id' => 'integer',
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

    /**
     * @param $stockCode
     * @return \App\Models\StockHolders | null
     */
    public function stock($stockCode)
    {
        return StockHolders::where('holder_id', $this->id)
            ->where('stock_code', $stockCode)
            ->first();
    }

    public function hasEnoughAvailableShares($stockCode, $shares)
    {
        $stockHolder = $this->stock($stockCode);
        return ! is_null($stockHolder) && $stockHolder->available_shares >= $shares;
    }
}
