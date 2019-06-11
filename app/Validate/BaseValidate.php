<?php
/**
 * User: gedongdong@
 * Date: 2019/1/11 下午3:42
 */

namespace App\Validate;


use Illuminate\Support\Facades\Validator;

class BaseValidate
{
    protected $rules;

    protected $message;

    // 请求数据
    public $requestData;

    protected $validator;

    protected $request;

    public $errors = null;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function goCheck()
    {
        $this->validator = Validator::make($this->request->all(), $this->rules, $this->message);

        $this->requestData = $this->validator->getData();

        $this->validator->after(function () {
            $this->customValidate();
        });

        if ($this->validator->fails()) {
            $this->errors = $this->validator->errors();
            return false;
        }

        return true;
    }

    /**
     * 自定义验证
     * 如需要，子类重写该方法
     */
    protected function customValidate()
    {
        // 子类重写
    }
}