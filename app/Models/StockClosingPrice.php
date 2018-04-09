<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="StockClosingPrice",
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
 *           property="closing_price",
 *           description="单日收盘价格",
 *           type="string",
 *           example="3.00000000",
 *       ),
 *       @SWG\Property(
 *           property="date",
 *           description="日期",
 *           type="string",
 *           example="2018-03-30",
 *       ),
 *       @SWG\Property(
 *           property="created_at",
 *           description="创建时间",
 *           type="string",
 *           example="2018-03-30 16:03:14",
 *       ),
 * )
 *
 */
class StockClosingPrice extends Model
{
    public $timestamps = false;
    protected $table = 'stock_closing_price';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
