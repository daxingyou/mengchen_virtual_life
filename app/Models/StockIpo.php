<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockIpo",
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
 *           property="stock_type_id",
 *           description="股票类型id",
 *           type="integer",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="issuer_id",
 *           description="发行人id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="ipo_price",
 *           description="ipo价格",
 *           type="number",
 *           example="1.1",
 *       ),
 *       @SWG\Property(
 *           property="ipo_shares",
 *           description="ipo股票数量",
 *           type="number",
 *           example="70",
 *       ),
 *       @SWG\Property(
 *           property="ipo_remained_shares",
 *           description="剩余ipo股票待认购数",
 *           type="number",
 *           example="50",
 *       ),
 *       @SWG\Property(
 *           property="dividend_policy_id",
 *           description="股票分红方案id",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="intro",
 *           description="股票简介",
 *           type="string",
 *           example="我的股票简介",
 *       ),
 *       @SWG\Property(
 *           property="status",
 *           description="ipo状态",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="ipo_subscribed_shares",
 *           description="ipo已认购股票数",
 *           type="number",
 *           example="20",
 *       ),
 *       allOf={
 *           @SWG\Schema(ref="#/definitions/CreatedAtUpdatedAt"),
 *       }
 * )
 *
 */
class StockIpo extends Model
{
    public $timestamps = true;
    protected $table = 'stock_ipo';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $appends = [
        'ipo_subscribed_shares'
    ];

    protected $casts = [
        //'ipo_price' => 'float',
        //'ipo_shares' => 'float',
        //'ipo_remained_shares' => 'float',
    ];

    public function getIpoSubscribedSharesAttribute()
    {
        return $this->attributes['ipo_shares'] - $this->attributes['ipo_remained_shares'];
    }
}
