<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use App\Http\Models\Menu;

class RouteService
{
    //获取所有路由
    public static function getRoutes()
    {
        $routes = [];

        $all_routes = app()->routes->getRoutes();
        foreach ($all_routes as $k => $value) {
            if (array_key_exists('as', $value->action) && !ends_with($value->action['as'], '.white')) {
                $routes[] = $value->action['as'];
            }
        }

        return $routes;
    }

    //获取所有路由名称以.index结尾的路由
    public static function getMenuRoutes()
    {
        $routes = [];

        //使用过的路由不能再添加
        $all_used_routes = Menu::whereNotNull('route')->pluck('route')->toArray();

        $all_routes = app()->routes->getRoutes();
        foreach ($all_routes as $k => $value) {
            if (array_key_exists('as', $value->action) && !ends_with($value->action['as'], '.white') && ends_with($value->action['as'], '.index') && !in_array($value->action['as'], $all_used_routes)) {
                $routes[] = $value->action['as'];
            }
        }

        return $routes;
    }
}
