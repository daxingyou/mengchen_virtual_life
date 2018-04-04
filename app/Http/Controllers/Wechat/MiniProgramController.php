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
 *             property="code",
 *             description="返回码，成功为-1",
 *             type="integer",
 *             format="int32",
 *             example="-1",
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
 *                 }
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
            'code' => -1,
            'message' => $msg,
        ];
    }
}
