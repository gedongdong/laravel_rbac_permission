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
use App\Http\Models\MenuRoles;
use App\Http\Models\Permission;
use App\Http\Models\RolePermission;
use App\Http\Models\Roles;
use App\Library\Response;
use App\Validate\RolesStoreValidate;
use App\Validate\RolesUpdateValidate;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Roles::paginate(config('page_size'));

        return view('admin.roles.index', ['roles' => $roles]);
    }

    public function create()
    {
        $permissions = Permission::select('id', 'name')->get();

        return view('admin.roles.create', ['permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        $validate = new RolesStoreValidate($request);

        if (!$validate->goCheck()) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => $validate->errors->first()]);
        }

        $params = $validate->requestData;

        DB::beginTransaction();

        try {
            $roles = new Roles();

            $roles->name = $params['name'];
            $roles->save();

            $pivot = [];
            foreach ($params['permission'] as $permission) {
                $pivot[] = [
                    'roles_id'      => $roles->id,
                    'permission_id' => $permission,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
            }
            RolePermission::insert($pivot);

            DB::commit();

            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();

            return Response::response(['code' => Response::SQL_ERROR]);
        }
    }

    public function edit(Request $request)
    {
        $role_id = $request->get('role_id');

        $error = '';
        $role  = null;

        $permission_ids = [];
        if (!$role_id) {
            $error = '参数有误';
        } else {
            $role = Roles::find($role_id);
            if (!$role) {
                $error = '获取权限信息错误';
            } else {
                //该角色具有的权限组id
                $permission_ids = RolePermission::where('roles_id', '=', $role_id)->pluck('permission_id')->toArray();
            }
        }

        //所有权限组
        $permissions = Permission::select('id', 'name')->get();

        return view('admin.roles.edit', ['permissions' => $permissions, 'error' => $error, 'permission_ids' => $permission_ids, 'role' => $role]);
    }

    public function update(Request $request)
    {
        $validate = new RolesUpdateValidate($request);

        if (!$validate->goCheck()) {
            return Response::response(['code' => Response::PARAM_ERROR, 'msg' => $validate->errors->first()]);
        }

        $params = $validate->requestData;

        DB::beginTransaction();

        try {
            $roles = Roles::find($params['id']);

            $roles->name = $params['name'];
            $roles->save();

            //删除旧的关联关系
            RolePermission::where('roles_id', '=', $params['id'])->delete();

            $pivot = [];
            foreach ($params['permission'] as $permission) {
                $pivot[] = [
                    'roles_id'      => $roles->id,
                    'permission_id' => $permission,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
            }
            RolePermission::insert($pivot);

            DB::commit();

            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('更新角色数据库异常', [$e->getMessage()]);

            return Response::response(['code' => Response::SQL_ERROR, 'e' => $e]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return Response::response(['code' => Response::PARAM_ERROR]);
        }

        DB::beginTransaction();

        try {
            Roles::where('id', $id)->delete();
            RolePermission::where('roles_id', $id)->delete();
            MenuRoles::where('roles_id', $id)->delete();
            DB::commit();

            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('删除角色数据库异常', [$e->getMessage()]);

            return Response::response(['code' => Response::PARAM_ERROR, 'e' => $e]);
        }
    }
}
