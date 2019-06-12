<?php
/**
 * Created by PhpStorm.
 * User: gedongdong
 * Date: 2019/5/6
 * Time: 下午10:12
 */

namespace App\Service;


class RouteService
{
    //获取所有路由
    public static function getRoutes()
    {
        $routes = [];

        $all_routes = app()->routes->getRoutes();
        foreach ($all_routes as $k => $value) {
            if (key_exists('as', $value->action) && !ends_with($value->action['as'], '.white')) {
                $routes[] = $value->action['as'];
            }
        }
        return $routes;
    }

    //获取所有路由名称以.index结尾的路由
    public static function getMenuRoutes()
    {
        $routes = [];

        $all_routes = app()->routes->getRoutes();
        foreach ($all_routes as $k => $value) {
            if (key_exists('as', $value->action) && !ends_with($value->action['as'], '.white') && ends_with($value->action['as'], '.index')) {
                $routes[] = $value->action['as'];
            }
        }
        return $routes;
    }
}