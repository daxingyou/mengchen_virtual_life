<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockDividendPolicy",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="policy",
 *           description="分会方案名称",
 *           type="string",
 *           example="定期身价分红",
 *       ),
 *       @SWG\Property(
 *           property="player_id",
 *           description="创建者玩家id",
 *           type="integer",
 *           example="10000",
 *       ),
 *       allOf={
 *           @SWG\Schema(ref="#/definitions/CreatedAtUpdatedAt"),
 *       }
 * )
 *
 */
class StockDividendPolicy extends Model
{
    public $timestamps = true;
    protected $table = 'stock_dividend_policy';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
