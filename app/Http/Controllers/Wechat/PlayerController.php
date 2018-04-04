<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;

class PlayerController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return \App\Models\Players
     *
     * @SWG\Get(
     *     path="/player/info",
     *     description="获取玩家个人信息",
     *     operationId="player.info",
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
        $player = $this->player($request)->append('stocks');
        return $player;
    }

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
