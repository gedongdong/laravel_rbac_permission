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
use App\Http\Models\Permission;
use App\Http\Models\RolePermission;
use App\Library\Response;
use App\Service\RouteService;
use App\Validate\PermissionStoreValidate;
use App\Validate\PermissionUpdateValidate;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(config('page_size'));

        return view('admin.permission.index', ['permissions' => $permissions]);
    }

    public function create()
    {
        $routes = RouteService::getRoutes();

        return view('admin.permission.create', ['routes' => $routes]);
    }

    public function store(Request $request)
    {
        $validate = new PermissionStoreValidate($request);

        if (!$validate->goCheck()) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => $validate->errors->first()]);
        }

        $params = $validate->requestData;

        $permission = new Permission();

        $permission->name = $params['name'];
        $permission->routes = implode(',', $params['route']);

        if (!$permission->save()) {
            return Response::response(['code' => Response::SQL_ERROR]);
        }

        return Response::response();
    }

    public function edit(Request $request)
    {
        $permission_id = $request->get('permission_id');

        $error = '';
        $permission = null;

        if (!$permission_id) {
            $error = '参数有误';
        } else {
            $permission = Permission::find($permission_id);
            if (!$permission) {
                $error = '获取权限信息错误';
            } else {
                $permission->routes = explode(',', $permission->routes);
            }
        }

        $routes = RouteService::getRoutes();

        return view('admin.permission.edit', ['permission' => $permission, 'error' => $error, 'routes' => $routes]);
    }

    public function update(Request $request)
    {
        $validate = new PermissionUpdateValidate($request);

        if (!$validate->goCheck()) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => $validate->errors->first()]);
        }

        $params = $validate->requestData;

        $permission = Permission::find($params['id']);

        $permission->name = $params['name'];
        $permission->routes = implode(',', $params['route']);

        if (!$permission->save()) {
            return Response::response(['code' => Response::SQL_ERROR]);
        }

        return Response::response();
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return Response::response(['code' => Response::PARAM_ERROR]);
        }

        DB::beginTransaction();

        try {
            Permission::where('id', $id)->delete();
            RolePermission::where('roles_id', $id)->delete();
            DB::commit();

            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('删除权限组数据库异常', [$e->getMessage()]);

            return Response::response(['code' => Response::SQL_ERROR, 'e' => $e]);
        }
    }
}
