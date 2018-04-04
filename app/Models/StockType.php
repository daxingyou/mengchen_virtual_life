<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockType",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="type_name",
 *           description="类型名称",
 *           type="string",
 *           example="音乐",
 *       ),
 *       @SWG\Property(
 *           property="creator_id",
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
class StockType extends Model
{
    public $timestamps = true;
    protected $table = 'stock_type';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
