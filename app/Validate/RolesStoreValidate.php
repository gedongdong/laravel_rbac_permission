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

class RolesStoreValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'name.unique' => '名称已存在',
        'name.required' => '请输入名称',
        'name.max' => '名称最长20个字符',
        'permission.required' => '请选择权限组',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'name' => 'required|unique:roles,name|max:20',
            'permission' => 'required',
        ];
    }

    protected function customValidate()
    {
        if (!array_key_exists('permission', $this->requestData)) {
            $this->validator->errors()->add('permission', '权限组参数不正确');

            return false;
        }

        $permissions = $this->requestData['permission'];

        $all_permissions = Permission::pluck('id')->toArray();
        if (count($permissions) != count(array_intersect($permissions, $all_permissions))) {
            $this->validator->errors()->add('permission', '权限组参数不正确');

            return false;
        }
    }
}
