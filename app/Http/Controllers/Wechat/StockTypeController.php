<?php

namespace App\Http\Controllers\Wechat;

use App\Models\StockType;
use Illuminate\Http\Request;

class StockTypeController extends MiniProgramController
{
    public function showStockType(Request $request)
    {
        return StockType::all();
    }

    public function addStockType(Request $request)
    {
        $this->validate($request, [
            'type_name' => 'required|string|max:255',
        ]);
        $player = $this->player($request);
        StockType::create([
            'type_name' => $request->input('type_name'),
            'creator_id' => $player->id,
        ]);
        return $this->res('操作成功');
    }
}
