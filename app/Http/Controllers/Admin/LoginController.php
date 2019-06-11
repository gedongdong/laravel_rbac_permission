<?php
/**
 * User: gedongdong@
 * Date: 2019/5/5 下午5:24
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Models\Users;
use App\Library\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        if (session('user')) {
            return redirect(route('admin.index.white'));
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
            'captcha'  => 'required|captcha',
        ], [
            'email.required'    => '请输入登录名',
            'email.email'       => '邮箱格式有误',
            'password.required' => '请输入密码',
            'captcha.required'  => '请输入验证码',
            'captcha.captcha'   => '验证码有误'
        ]);

        if ($validator->fails()) {
            return Response::response(Response::PARAM_ERROR, $validator->errors()->first());
        }

        $data = $validator->getData();

        $user = Users::where('email', '=', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return Response::response(Response::BAD_REQUEST, '登录名或密码有误');
        }

        if ($user->status == Users::STATUS_DISABLE) {
            return Response::response(Response::BAD_REQUEST, '您的账户被禁用，请联系管理员');
        }

        session(['user' => $user]);

        return Response::response();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');

        return redirect(route('admin.login.white'));
    }
}