<?php

namespace App\Exceptions;

use Exception;

class WechatMiniProgramCommonException extends Exception
{
    protected $code;

    /**
     * 自定义的异常类，用于返回error json响应
     * @param string $message
     */
    public function __construct($message = '', $code = null, Exception $previous = null)
    {
        $this->code = ! is_null($code) ? $code : config('exceptions.WechatMiniProgramCommonException', 0);
        parent::__construct($message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'code' => $this->getCode(),
            'error' => $this->getMessage(),
        ], 200, [], JSON_UNESCAPED_UNICODE);    //前端需要返回200
    }
}