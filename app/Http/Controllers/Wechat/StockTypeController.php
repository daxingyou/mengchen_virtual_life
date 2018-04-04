<?php

namespace App\Http\Controllers\Wechat;

use App\Models\StockType;
use Illuminate\Http\Request;

class StockTypeController extends MiniProgramController
{
    /**
     * @param Request $request
     * @return \App\Models\StockType
     *
     * @SWG\Get(
     *     path="/stock/type",
     *     description="获取所有股票分类类型",
     *     operationId="stock.type.get",
     *     tags={"stock"},
     *
     *     @SWG\Response(
     *         response=200,
     *         description="返回所有股票分类类型",
     *         @SWG\Property(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 allOf={
     *                     @SWG\Schema(ref="#/definitions/StockType"),
     *                 }
     *             ),
     *         ),
     *     ),
     * )
     */
    public function showStockType(Request $request)
    {
        return StockType::all();
    }

    /**
     * @param Request $request
     * @return array
     *
     * @SWG\Post(
     *     path="/stock/type",
     *     description="添加股票类型",
     *     operationId="stock.type.add",
     *     tags={"stock"},
     *
     *     @SWG\Parameter(
     *         name="type_name",
     *         description="类型名称",
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
