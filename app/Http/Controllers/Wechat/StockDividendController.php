<?php

namespace App\Http\Controllers\Wechat;

use App\Exceptions\WechatMiniProgramCommonException;
use App\Models\StockDividendPolicy;
use Illuminate\Http\Request;

class StockDividendController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return \App\Models\StockDividendPolicy
     *
     * @SWG\Get(
     *     path="/stock/dividend-policy",
     *     description="获取股票分红方案",
     *     operationId="stock.dividend-policy.get",
     *     tags={"stock"},
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回所有股票分红方案",
     *         @SWG\Property(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 allOf={
     *                     @SWG\Schema(ref="#/definitions/StockDividendPolicy"),
     *                 }
     *             ),
     *         ),
     *     ),
     * )
     */
    public function showDividendPolicy(Request $request)
    {
        return StockDividendPolicy::all();
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Post(
     *     path="/stock/dividend-policy",
     *     description="添加股票分红方案",
     *     operationId="stock.dividend-policy.add",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="policy",
     *         description="分红方案名称",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="添加成功",
     *         @SWG\Schema(
     *             ref="#/definitions/Success",
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="请求参数验证失败",
     *         @SWG\Schema(
     *             ref="#/definitions/ValidationError",
     *         ),
     *     ),
     * )
     */
    public function addDividendPolicy(Request $request)
    {
        $this->validate($request, [
            'policy' => 'required|string|max:255',
        ]);

        $player = $this->player($request);
        StockDividendPolicy::create([
            'policy' => $request->input('policy'),
            'player_id' => $player->id,
        ]);

        return $this->res('添加成功');
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Delete(
     *     path="/stock/dividend-policy",
     *     description="删除股票分红方案",
     *     operationId="stock.dividend-policy.del",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="policy_id",
     *         description="分红方案id",
     *         in="query",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="删除成功",
     *         @SWG\Schema(
     *             ref="#/definitions/Success",
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="通用错误",
     *         @SWG\Schema(
     *             ref="#/definitions/CommonError",
     *         ),
     *     ),
     * )
     */
    public function delDividendPolicy(Request $request)
    {
        $policy = StockDividendPolicy::find($request->input('policy_id'));
        throw_if(empty($policy), WechatMiniProgramCommonException::class, 'policy不存在');

        $player = $this->player($request);
        throw_if($policy->player_id !== $player->id, WechatMiniProgramCommonException::class
            , '只能操作您自己创建的policy');

        $policy->delete();
        return $this->res('删除成功');
    }
}
