<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Validate;

use App\Service\RouteService;

class PermissionStoreValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'name.unique' => '名称已存在',
        'name.required' => '请输入名称',
        'name.max' => '名称最长20个字符',
        'route.required' => '请选择路由',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'name' => 'required|unique:permission,name|max:20',
            'route' => 'required',
        ];
    }

    protected function customValidate()
    {
        $route = $this->requestData['route'] ?? '';

        if (!$route) {
            $this->validator->errors()->add('route', '请选择路由');

            return false;
        }

        $all_routes = RouteService::getRoutes();
        if (count($route) != count(array_intersect($route, $all_routes))) {
            $this->validator->errors()->add('route', '路由参数不正确');

            return false;
        }
    }
}
