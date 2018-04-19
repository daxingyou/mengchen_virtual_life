<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Services\WechatMiniProgramService;
use Illuminate\Http\Request;

/**
 * @SWG\Swagger(
 *     basePath="/wechat",
 *     host=L5_SWAGGER_CONST_HOST,
 *     schemes={"http"},
 *     consumes={"application/json"},
 *
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Virtual Life API",
 *         description="模拟人生接口",
 *         @SWG\Contact(name="Dian"),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="Success",
 *         type="object",
 *         @SWG\Property(
 *             property="success",
 *             description="执行结果",
 *             type="boolean",
 *             default="true",
 *         ),
 *         @SWG\Property(
 *             property="code",
 *             description="返回码，成功为-1",
 *             type="integer",
 *             format="int32",
 *             default="-1",
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             description="消息",
 *             type="string",
 *             example="操作成功",
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="ValidationError",
 *         type="object",
 *         @SWG\Property(
 *             property="message",
 *             description="参数验证失败提示消息",
 *             type="string",
 *         ),
 *         @SWG\Property(
 *             property="errors",
 *             description="错误详情",
 *             type="object",
 *             ref="#/definitions/ValidationErrorDetails",
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="ValidationErrorDetails",
 *         description="key为验证失败的参数名, 值为所有验证失败的条目(数组)",
 *         type="object",
 *         @SWG\Property(
 *             property="stock_code",
 *             example={"stock_code 不能大于 1 个字符", "stock_code 应该为数字"},
 *             type="array",
 *             @SWG\Items(
 *                 type="string",
 *                 description="参数验证失败详情",
 *             ),
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="CreatedAtUpdatedAt",
 *         type="object",
 *         @SWG\Property(
 *             property="created_at",
 *             description="创建时间",
 *             type="string",
 *             example="2018-03-30 16:03:14",
 *         ),
 *         @SWG\Property(
 *             property="updated_at",
 *             description="更新时间",
 *             type="string",
 *             example="2018-03-30 17:14:42",
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="PlayerStock",
 *         description="玩家模型（包含所持股票）",
 *         type="object",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/Player"),
 *         },
 *         @SWG\Property(
 *             property="stocks",
 *             description="所持股票",
 *             type="array",
 *             @SWG\Items(
 *                 type="object",
 *                 allOf={
 *                     @SWG\Schema(ref="#/definitions/StockHolder"),
 *                 },
 *                 @SWG\Property(
 *                     property="changing_rate",
 *                     description="涨跌幅",
 *                     type="string",
 *                     example="-0.2500",
 *                 ),
 *                 @SWG\Property(
 *                     property="last_price",
 *                     description="最新成交价",
 *                     type="string",
 *                     example="3.00000000",
 *                 ),
 *             ),
 *         ),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="StockDepth",
 *         description="股票交易深度",
 *         type="object",
 *         @SWG\Property(
 *             property="buy",
 *             description="买单深度",
 *             type="array",
 *             @SWG\Items(
 *                 type="array",
 *                 example={"9.00000000", "2.00000000"},
 *                 @SWG\Items(
 *                     type="string",
 *                     minItems=2,
 *                     maxItems=2,
 *                 ),
 *             ),
 *         ),
 *         @SWG\Property(
 *             property="sell",
 *             description="卖单深度",
 *             type="array",
 *             @SWG\Items(
 *                 type="array",
 *                 example={"10.00000000", "2.00000000"},
 *                 @SWG\Items(
 *                     type="string",
 *                     minItems=2,
 *                     maxItems=2,
 *                 ),
 *             ),
 *         ),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="StockTicker",
 *         description="Ticker",
 *         type="object",
 *         @SWG\Property(
 *             property="stock_code",
 *             description="股票代码",
 *             type="string",
 *             example="ABC12345",
 *         ),
 *         @SWG\Property(
 *             property="price",
 *             description="成交价格",
 *             type="string",
 *             example="3.00000000",
 *         ),
 *         @SWG\Property(
 *             property="shares",
 *             description="成交数量",
 *             type="string",
 *             example="4.00000000",
 *         ),
 *         @SWG\Property(
 *             property="take_direction",
 *             description="成交方向",
 *             type="string",
 *             example="sell",
 *         ),
 *         @SWG\Property(
 *             property="created_at",
 *             description="成交",
 *             type="string",
 *             example="2018-03-30 16:03:14",
 *         ),
 *         @SWG\Property(
 *             property="highest_price",
 *             description="历史最高价",
 *             type="string",
 *             example="4.00000000",
 *         ),
 *         @SWG\Property(
 *             property="lowest_price",
 *             description="历史最低价",
 *             type="string",
 *             example="0.60000000",
 *         ),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="StockTrend",
 *         description="股票涨跌幅变化",
 *         type="object",
 *         @SWG\Property(
 *             property="{stock_code}",
 *             description="此只股票的趋势",
 *             type="object",
 *             @SWG\Property(
 *                 property="changing_rate",
 *                 description="涨跌幅",
 *                 type="string",
 *                 example="-0.2500",
 *             ),
 *             @SWG\Property(
 *                 property="last_price",
 *                 description="最新成交价(可能最近的成交在3天之前)",
 *                 type="string",
 *                 example="3.00000000",
 *             ),
 *             @SWG\Property(
 *                 property="today_last_price",
 *                 description="进入最新成交价(无则为0)",
 *                 type="string",
 *                 example="3.00000000",
 *             ),
 *             @SWG\Property(
 *                 property="owner",
 *                 description="此只股票的所有者",
 *                 type="object",
 *                 allOf={
 *                     @SWG\Schema(ref="#/definitions/Player"),
 *                 },
 *             ),
 *         ),
 *     ),
 * )
 */
class MiniProgramController extends Controller
{
    public function player(Request $request)
    {
        return WechatMiniProgramService::getPlayer($request);
    }

    public function res($msg)
    {
        return [
            'success' => true,
            'code' => -1,
            'message' => $msg,
        ];
    }
}
