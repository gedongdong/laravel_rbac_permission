<?php
/**
 * User: gedongdong@
 * Date: 2019/1/31 下午8:01
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

    static $errMsg = [
        self::BAD_REQUEST  => '请求错误',
        self::PARAM_ERROR  => '参数错误',
        self::SQL_ERROR    => '数据库执行错误',
        self::SERVER_ERROR => '服务器错误',
    ];

    public static function response($code = 0, $msg = '', $data = [])
    {
        $msg = $msg ?: (key_exists($code, self::$errMsg) ? self::$errMsg[$code] : '未知错误');

        return response(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }
}