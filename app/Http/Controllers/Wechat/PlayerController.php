<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;

class PlayerController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Get(
     *     path="/player/info",
     *     description="获取玩家个人信息",
     *     operationId="player.info.get",
     *     tags={"player"},
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回玩家个人信息和所持股票",
     *         @SWG\Schema(
     *             ref="#/definitions/PlayerStock",
     *         ),
     *     ),
     * )
     */
    public function getInfo(Request $request)
    {
        $player = $this->player($request)->append('stocks')->toArray();

        //此玩家所拥有的每只股票数据加上涨跌幅和最新成交价数据
        $ctrl = \App::make(\App\Http\Controllers\Wechat\StockMarketController::class);
        $trend = \App::call([$ctrl, 'getTrend']);
        foreach ($player['stocks'] as &$stock) {
            if (isset($trend[$stock['stock_code']])) {
                $stock['changing_rate'] = $trend[$stock['stock_code']]['changing_rate'];
                $stock['last_price'] = $trend[$stock['stock_code']]['last_price'];
            } else {
                $stock['changing_rate'] = 0;
                $stock['last_price'] = 0;
            }
        }

        return $player;
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Put(
     *     path="/player/info",
     *     description="更新玩家信息",
     *     operationId="player.info.update",
     *     tags={"player"},
     *
     *     @SWG\Parameter(
     *         name="nickname",
     *         description="昵称",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="gender",
     *         description="性别(0-女，1-男)",
     *         in="query",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="profession",
     *         description="职业",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="real_name",
     *         description="真实姓名",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="identity_id",
     *         description="身份证号",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="intro",
     *         description="简介",
     *         in="query",
     *         required=false,
     *         type="string",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="操作成功",
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
    public function updateInfo(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'nullable|string|max:255',
            'gender' => 'nullable|integer|in:0,1',
            'profession' => 'nullable|string|max:255',
            'real_name' => 'nullable|string|max:255',
            'identity_id' => 'nullable|string|max:18',
            'intro' => 'nullable|string|max:255',
        ]);
        $data = $request->only([
            'nickname', 'gender', 'profession', 'real_name', 'identity_id', 'intro',
        ]);

        $player = $this->player($request);
        $player->update($data);

        return $this->res('操作成功');
    }
}
