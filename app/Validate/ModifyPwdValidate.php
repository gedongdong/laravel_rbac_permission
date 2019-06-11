<?php
/**
 * User: gedongdong@
 * Date: 2019/1/28 下午9:20
 */

namespace App\Validate;


class ModifyPwdValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'password.required'        => '请输入当前密码',
        'password.between'         => '密码长度为6-20个字符',
        'password_repeat.required' => '请输入确认密码',
        'password_repeat.same'     => '两次填写的密码不一致',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'password'        => 'required|between:6,20',
            'password_repeat' => 'required|same:password',
        ];
    }

}