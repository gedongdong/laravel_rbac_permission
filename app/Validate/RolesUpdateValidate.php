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
use App\Http\Models\Roles;

class RolesUpdateValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'id.required' => 'ID参数不能为空',
        'id.numeric' => 'ID参数不正确',
        'name.required' => '请输入名称',
        'name.max' => '名称最长20个字符',
        'permission.required' => '请选择权限组',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'id' => 'required|numeric',
            'name' => 'required|max:20',
            'permission' => 'required',
        ];
    }

    protected function customValidate()
    {
        $id = $this->requestData['id'];
        $name = $this->requestData['name'];
        $permissions = $this->requestData['permission'];

        if (!Roles::find($id)) {
            $this->validator->errors()->add('id', '角色信息不正确');

            return false;
        }

        if (Roles::where('id', '!=', $id)->where('name', '=', $name)->count() > 0) {
            $this->validator->errors()->add('name', '该名称已存在');

            return false;
        }

        $all_permissions = Permission::pluck('id')->toArray();
        if (count($permissions) != count(array_intersect($permissions, $all_permissions))) {
            $this->validator->errors()->add('route', '权限组参数不正确');

            return false;
        }
    }
}
