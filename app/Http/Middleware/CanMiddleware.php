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

use App\Library\Response;
use Closure;

class CanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!can()) {
            if ($request->ajax()) {
                return response(['code' => Response::FORBIDDEN, 'msg' => '您没有被授权访问', 'data' => []]);
            }

            return redirect(route('admin.forbidden.white'));
        }

        return $next($request);
    }
}
