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


class CategoryStoreValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'name.unique' => '名称已存在',
        'name.required' => '请输入名称',
        'name.max' => '名称最长20个字符'
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'name' => 'required|unique:roles,name|max:20',
        ];
    }
}
