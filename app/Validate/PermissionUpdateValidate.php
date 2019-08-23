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

use App\Http\Models\Permission;
use App\Service\RouteService;

class PermissionUpdateValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'id.required' => 'ID参数不能为空',
        'id.numeric' => 'ID参数不正确',
        'name.required' => '请输入名称',
        'name.max' => '名称最长20个字符',
        'route.required' => '请选择路由',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'id' => 'required|numeric',
            'name' => 'required|max:20',
            'route' => 'required',
        ];
    }

    protected function customValidate()
    {
        $id = $this->requestData['id'];
        $name = $this->requestData['name'];

        if (!Permission::find($id)) {
            $this->validator->errors()->add('id', '权限信息不正确');

            return false;
        }

        if (Permission::where('id', '!=', $id)->where('name', '=', $name)->count() > 0) {
            $this->validator->errors()->add('name', '该名称已存在');

            return false;
        }

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
