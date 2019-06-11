<?php
/**
 * User: gedongdong@
 * Date: 2019/5/5 下午9:20
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Models\Users;
use App\Library\Response;
use App\Validate\ModifyPwdValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function modifyPwd()
    {
        return view('admin.modifyPwd');
    }

    public function newPwd(Request $request)
    {
        $validate = new ModifyPwdValidate($request);
        if (!$validate->goCheck()) {
            return Response::response(Response::PARAM_ERROR, $validate->errors->first());
        }

        $params = $validate->requestData;

        $user = Users::find(session('user')['id']);
        if (!$user) {
            return Response::response(Response::BAD_REQUEST);
        }

        if (!Hash::check($params['oldPassword'], $user->password)) {
            return Response::response(Response::BAD_REQUEST, '请输入正确的当前密码');
        }

        $user->password = Hash::make($params['password']);

        if ($user->save()) {
            //退出登录
            $request->session()->forget('user');
            return Response::response();
        }
        return Response::response(Response::SQL_ERROR);
    }
}