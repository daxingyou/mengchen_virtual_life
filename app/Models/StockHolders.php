<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockHolder",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="stock_code",
 *           description="股票代码",
 *           type="string",
 *           example="ABC12345",
 *       ),
 *       @SWG\Property(
 *           property="holder_id",
 *           description="持有人玩家id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="total_shares",
 *           description="持有总股票数（包括冻结）",
 *           type="string",
 *           example="5.00000000",
 *       ),
 *       @SWG\Property(
 *           property="frozen_shares",
 *           description="冻结股票数",
 *           type="string",
 *           example="3.00000000",
 *       ),
 *       @SWG\Property(
 *           property="available_shares",
 *           description="可用股票数",
 *           type="string",
 *           example="2.00000000",
 *       ),
 *       allOf={
 *           @SWG\Schema(ref="#/definitions/CreatedAtUpdatedAt"),
 *       }
 * )
 *
 */
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

    protected $appends = [
        'available_shares',
    ];

    protected $casts = [
        //'total_shares' => 'float',
        //'frozen_shares' => 'float',
    ];

    public function getAvailableSharesAttribute()
    {
        return $this->attributes['total_shares'] - $this->attributes['frozen_shares'];
    }
}
