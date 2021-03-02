<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies;

    /**
     * The current proxy header mappings.
     *
     * @var array
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
    //protected $headers = [
    //    Request::HEADER_FORWARDED => 'FORWARDED',
    //    Request::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
    //    Request::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
    //    Request::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
    //    Request::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
    //];
}
