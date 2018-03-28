<?php

namespace App\Http\Controllers\Wechat;

use App\Models\Configuration;
use App\Models\GameReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetController extends MiniProgramController
{
    public function interact(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|string|max:255',
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
