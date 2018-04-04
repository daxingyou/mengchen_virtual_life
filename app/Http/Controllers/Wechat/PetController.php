<?php

namespace App\Http\Controllers\Wechat;

use App\Models\Configuration;
use App\Models\GameReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Post(
     *     path="/pet/interaction",
     *     description="宠物交互",
     *     operationId="pet.interaction",
     *     tags={"pet"},
     *
     *     @SWG\Parameter(
     *         description="动作名称（抚摸，喂养等）",
     *         in="query",
     *         name="action",
     *         required=true,
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
    public function interact(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|string|max:1',
        ]);
        $action = $request->input('action');
        $player = $this->player($request);
        $configuration = Configuration::findOrFail(1);
        DB::transaction(function () use ($player, $action, $configuration) {
            //创建游戏记录
            $rewardLog = GameReward::create([
                'player_id' => $player->id,
                'action' => $action,
                'reward' => $configuration->pet_reward,    //游戏身价奖励
                'pet_exp' => $configuration->pet_exp,   //宠物经验增加
            ]);

            //增加玩家的游戏身价
            $player->game_points += $rewardLog->reward;
            $player->save();

            //增加玩家的宠物经验
            $player->player_pet->update([
                'pet_exp' => $player->player_pet->pet_exp += $rewardLog->pet_exp,
            ]);
        });

        return $this->res('操作成功');
    }
}
