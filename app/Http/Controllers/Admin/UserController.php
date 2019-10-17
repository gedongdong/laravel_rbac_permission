<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Roles;
use App\Http\Models\Users;
use App\Http\Models\UsersRoles;
use App\Library\Response;
use App\Validate\UserStoreValidate;
use App\Validate\UserUpdateValidate;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = Users::select('id', 'email', 'name', 'administrator', 'status', 'created_at')->paginate(config('page_size'));

        return view('admin.user.index', ['users' => $users]);
    }

    public function create()
    {
        $roles = Roles::all();

        return view('admin.user.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validate = new UserStoreValidate($request);
        if (!$validate->goCheck()) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => $validate->errors->first()]);
        }

        $params = $validate->requestData;

        DB::beginTransaction();

        try {
            $user = new Users();

            $user->name          = $params['name'];
            $user->email         = $params['email'];
            $user->password      = Hash::make($params['password']);
            $user->status        = $params['status'];
            $user->administrator = $params['administrator'];
            $user->creator_id    = session('user')['id'];
            $user->save();

            $roles = $params['roles'] ?? '';
            if ($roles) {
                $pivot = [];
                foreach ($params['roles'] as $role) {
                    $pivot[] = [
                        'users_id'   => $user->id,
                        'roles_id'   => $role,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                UsersRoles::insert($pivot);
            }

            DB::commit();

            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();

            return Response::response(['code' => Response::SQL_ERROR]);
        }
    }

    public function edit(Request $request)
    {
        $user_id = $request->get('user_id');

        $error    = '';
        $user     = null;
        $role_ids = [];
        if (!$user_id) {
            $error = '参数有误';
        } else {
            $user = Users::find($user_id);
            if (!$user) {
                $error = '用户信息错误';
            } else {
                $role_ids = UsersRoles::where('users_id', '=', $user_id)->pluck('roles_id')->toArray();
            }
        }

        $roles = Roles::all();

        return view('admin.user.edit', ['roles' => $roles, 'error' => $error, 'role_ids' => $role_ids, 'user' => $user]);
    }

    public function update(Request $request)
    {
        $validate = new UserUpdateValidate($request);
        if (!$validate->goCheck()) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => $validate->errors->first()]);
        }

        $params = $validate->requestData;

        DB::beginTransaction();

        try {
            $user = Users::find($params['id']);

            $user->name  = $params['name'];
            $user->email = $params['email'];
            //$user->status        = $params['status'];
            $user->administrator = $params['administrator'];

            $password = $params['password'] ?? '';
            if ($password) {
                $user->password = Hash::make($params['password']);
            }
            $user->save();

            //删除原有用户-角色关系
            UsersRoles::where('users_id', '=', $params['id'])->delete();

            $roles = $params['roles'] ?? '';
            if ($roles && Users::ADMIN_NO == $user->administrator) {
                $pivot = [];
                foreach ($params['roles'] as $role) {
                    $pivot[] = [
                        'users_id'   => $user->id,
                        'roles_id'   => $role,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                UsersRoles::insert($pivot);
            }

            DB::commit();

            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();

            return Response::response(['code' => Response::SQL_ERROR, 'e' => $e]);
        }
    }

    /**
     * 修改用户状态
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function status(Request $request)
    {
        $user_id = $request->get('user_id');
        if (!$user_id) {
            return Response::response(['code' => Response::PARAM_ERROR]);
        }

        if ($user_id == session('user')['id']) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => '你不能修改自己的状态']);
        }

        $user = Users::find($user_id);
        if (!$user) {
            return Response::response(['code' => Response::BAD_REQUEST]);
        }

        if (Users::ADMIN_YES == $user->administrator && Users::STATUS_ENABLE == $user->status) {
            //除了当前管理员，至少有一个启用状态的管理员
            if (Users::where('id', '!=', $user_id)->where('administrator', '=', Users::ADMIN_YES)->where('status', '=', Users::STATUS_ENABLE)->count() <= 0) {
                return Response::response(['code' => Response::BAD_REQUEST, 'msg' => '至少有一个管理员']);
            }
        }

        $user->status = Users::STATUS_ENABLE == $user->status ? Users::STATUS_DISABLE : Users::STATUS_ENABLE;

        if (!$user->save()) {
            return Response::response(['code' => Response::SQL_ERROR]);
        }

        return Response::response();
    }

    public function reset(Request $request)
    {
        $user_id = $request->get('id');
        if (!$user_id) {
            return Response::response(['code' => Response::PARAM_ERROR]);
        }

        $user = Users::find($user_id);
        if (!$user || Users::STATUS_ENABLE != $user->status) {
            //启用的用户才可以重置密码
            return Response::response(['code' => Response::BAD_REQUEST]);
        }

        if ($user_id == 1) {
            //公共测试环境暂不允许修改超管密码~
            return Response::response(['code' => Response::BAD_REQUEST, 'msg' => '公共测试环境暂不允许修改超管密码~']);
        }

        //统一重置密码为admin123
        $user->password = Hash::make('admin123');

        if (!$user->save()) {
            return Response::response(['code' => Response::SQL_ERROR]);
        }

        return Response::response(['code' => Response::OK, 'msg' => '密码已成功重置为：admin123']);
    }
}
