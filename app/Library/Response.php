<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Library;

class Response
{
    const OK = 0;

    const BAD_REQUEST = 1000;

    const PARAM_ERROR = 1001;

    const SQL_ERROR = 4000;

    const FORBIDDEN = 4003;

    const SERVER_ERROR = 5000;

    public static $errMsg = [
        self::BAD_REQUEST  => '请求错误',
        self::PARAM_ERROR  => '参数错误',
        self::SQL_ERROR    => '数据库执行错误',
        self::SERVER_ERROR => '服务器错误',
    ];

    public static function response(array $params = [])
    {
        $data = $params['data'] ?? [];
        if (env('APP_DEBUG') && array_key_exists('e', $params) && $params['e'] instanceof \Exception) {
            $code = $params['e']->getCode();
            $msg  = $params['e']->getMessage();
        } else {
            $code = $params['code'] ?? 0;
            $msg  = $params['msg'] ?? (array_key_exists($code, self::$errMsg) ? self::$errMsg[$code] : '未知错误');
        }

        return response(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }
}
