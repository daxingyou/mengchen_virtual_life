<?php

namespace App\Exceptions;

use Exception;

/**
 * @SWG\Definition(
 *     definition="CommonError",
 *     type="object",
 *     @SWG\Property(
 *         property="success",
 *         description="结果(false)",
 *         type="boolean",
 *         default=false,
 *     ),
 *     @SWG\Property(
 *         property="code",
 *         description="返回码，大于等于0",
 *         type="integer",
 *         format="int32",
 *         example="2001",
 *     ),
 *     @SWG\Property(
 *         property="error",
 *         description="错误消息提示",
 *         type="string",
 *         example="原密码错误",
 *     ),
 * ),
 */
class CustomException extends Exception
{
    protected $code;

    /**
     * 自定义的异常类，用于返回error json响应
     * @param string $message
     */
    public function __construct($message = '', $code = null, Exception $previous = null)
    {
        $this->code = ! is_null($code) ? $code : config('exceptions.CustomException', 0);
        parent::__construct($message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'code' => $this->getCode(),
            'error' => $this->getMessage(),
        ], 400, [], JSON_UNESCAPED_UNICODE);
    }
}