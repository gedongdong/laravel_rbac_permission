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

class UserUpdateValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'id.required' => 'ID参数不能为空',
        'id.numeric' => 'ID参数错误',
        'name.required' => '请输入姓名',
        'name.max' => '姓名最多20个字符',
        'email.required' => '请输入邮箱',
        'email.email' => '邮箱格式不正确',
        'password.between' => '密码长度为6-20个字符',
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
            'id' => 'required|numeric',
            'name' => 'required|max:20',
            'email' => 'required|email',
            'password' => 'nullable|between:6,20',
            'password_repeat' => 'nullable|same:password',
            //'status'          => ['required', Rule::in([1, 2])],
            'administrator' => ['required', Rule::in([1, 2])],
            'roles' => 'sometimes',
        ];
    }

    protected function customValidate()
    {
        $roles = $this->requestData['roles'] ?? '';
        $administrator = $this->requestData['administrator'];
        $id = $this->requestData['id'];
        $email = $this->requestData['email'];

        if (Users::ADMIN_NO == $administrator) {
            if (!$roles) {
                $this->validator->errors()->add('roles', '请选择所属角色');

                return false;
            }
            if (Users::where('id', '!=', $id)->where('administrator', Users::ADMIN_YES)->count() <= 0) {
                $this->validator->errors()->add('roles', '至少有一个超级管理员');

                return false;
            }
        }

        if (!Users::find($id)) {
            $this->validator->errors()->add('id', 'ID参数有误');

            return false;
        }

        $user = session()->get('user');
        if ($user['id'] == $id && Users::ADMIN_YES == $user['administrator'] && Users::ADMIN_NO == $administrator) {
            $this->validator->errors()->add('roles', '你不能修改自己的超级管理员配置');

            return false;
        }

        if (Users::where('id', '!=', $id)->where('email', '=', $email)->count() > 0) {
            $this->validator->errors()->add('name', '邮箱已经存在');

            return false;
        }
    }
}
