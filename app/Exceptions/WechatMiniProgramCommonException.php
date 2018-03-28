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
    public function __construct($message = '', Exception $previous = null)
    {
        $this->code = config('exceptions.WechatMiniProgramCommonException', 0);
        parent::__construct($message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'code' => $this->getCode(),
            'error' => $this->getMessage(),
            'result' => false,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}