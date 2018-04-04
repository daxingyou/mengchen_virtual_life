<?php

namespace App\Http\Controllers\Wechat;

use App\Models\Configuration;
use App\Models\PointsSold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointsController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Post(
     *     path="/points/purchase",
     *     description="购买身价",
     *     operationId="point.purchase",
     *     tags={"point"},
     *
     *     @SWG\Parameter(
     *         name="points",
     *         description="身价数量",
     *         in="query",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="购买成功",
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
    public function purchase(Request $request)
    {
        $request->validate([
            'points' => 'required|integer',
        ]);

        $points = $request->input('points');
        $player = $this->player($request);
        $configuration = Configuration::findOrFail(1);

        DB::transaction(function () use ($points, $player, $configuration) {
            //记录此次售卖记录
            PointsSold::create([
                'buyer_id' => $player->id,
                'sold_points' => $points,
            ]);

            //增加玩家的身价点数
            $player->points += $points;
            $player->save();
        });

        return $this->res('购买成功');
    }
}
