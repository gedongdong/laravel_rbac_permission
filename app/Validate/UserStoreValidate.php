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

use App\Http\Models\Users;
use Illuminate\Validation\Rule;

class UserStoreValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'name.required' => '请输入姓名',
        'name.max' => '姓名最多20个字符',
        'email.required' => '请输入邮箱',
        'email.email' => '邮箱格式不正确',
        'email.unique' => '邮箱已经存在',
        'password.required' => '请输入密码',
        'password.between' => '密码长度为6-20个字符',
        'password_repeat.required' => '请输入确认密码',
        'password_repeat.same' => '两次填写的密码不一致',
        'status.required' => '请选择状态',
        'status.in' => '状态值不正确',
        'administrator.required' => '请选择是否管理员',
        'administrator.in' => '管理员参数不正确',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'name' => 'required|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|between:6,20',
            'password_repeat' => 'required|same:password',
            'status' => ['required', Rule::in([1, 2])],
            'administrator' => ['required', Rule::in([1, 2])],
            'roles' => 'sometimes',
        ];
    }

    protected function customValidate()
    {
        $roles = $this->requestData['roles'] ?? '';
        $administrator = $this->requestData['administrator'];

        if (Users::ADMIN_NO == $administrator && !$roles) {
            $this->validator->errors()->add('roles', '请选择所属角色');

            return false;
        }
    }
}
