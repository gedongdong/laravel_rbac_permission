<?php
/**
 * User: gedongdong@
 * Date: 2019/5/6 上午10:11
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Models\Menu;
use App\Http\Models\MenuRoles;
use App\Http\Models\Roles;
use App\Library\Response;
use App\Service\RouteService;
use App\Validate\MenuStoreValidate;
use App\Validate\MenuUpdateValidate;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index()
    {
        $menu     = Menu::with('roles')->select('id', 'name as title', 'pid', 'route', 'created_at')->get()->toArray();
        $menu_arr = [];
        foreach ($menu as $m) {
            $menu_arr[] = [
                'id'         => $m['id'],
                'title'      => $m['title'],
                'pid'        => $m['pid'],
                'route'      => $m['route'],
                'created_at' => $m['created_at'],
                'roles'      => array_column($m['roles'], 'name')
            ];
        }
        return view('admin.menu.index', ['menu' => json_encode($menu_arr)]);
    }

    public function create()
    {
        $top_menu = Menu::with('roles')->where('pid', '=', 0)->select('id', 'name')->get();
        $routes   = RouteService::getMenuRoutes();
        $roles    = Roles::all();
        return view('admin.menu.create', ['top_menu' => $top_menu, 'routes' => $routes, 'roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validate = new MenuStoreValidate($request);

        if (!$validate->goCheck()) {
            return Response::response(Response::PARAM_ERROR, $validate->errors->first());
        }

        $params = $validate->requestData;

        DB::beginTransaction();
        try {
            $menu = new Menu();

            $menu->name  = $params['name'];
            $menu->pid   = $params['pid'];
            $menu->route = $params['pid'] == 0 ? null : $params['route'];
            $menu->save();

            $pivot = [];
            foreach ($params['role'] as $role) {
                $pivot[] = [
                    'menu_id'    => $menu->id,
                    'roles_id'   => $role,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            MenuRoles::insert($pivot);

            DB::commit();
            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('角色创建数据库异常', [$e->getMessage()]);
            return Response::response(Response::SQL_ERROR);
        }
    }

    public function edit(Request $request)
    {
        $menu_id = $request->get('menu_id');

        $error = '';
        $menu  = null;

        $role_ids = [];
        if (!$menu_id) {
            $error = '参数有误';
        } else {
            $menu = Menu::find($menu_id);
            if (!$menu) {
                $error = '获取菜单信息错误';
            } else {
                //该菜单具有的角色组id
                $role_ids = MenuRoles::where('menu_id', '=', $menu_id)->pluck('roles_id')->toArray();
            }
        }

        //所有角色组
        $roles = Roles::select('id', 'name')->get();

        //获取顶级菜单，排除当前菜单
        $top_menu = Menu::with('roles')->where('pid', '=', 0)->where('id', '!=', $menu_id)->select('id', 'name')->get();

        //获取所有路由标识
        $routes = RouteService::getMenuRoutes();

        return view('admin.menu.edit', ['roles' => $roles, 'error' => $error, 'role_ids' => $role_ids, 'menu' => $menu, 'top_menu' => $top_menu, 'routes' => $routes]);
    }

    public function update(Request $request)
    {
        $validate = new MenuUpdateValidate($request);

        if (!$validate->goCheck()) {
            return Response::response(Response::PARAM_ERROR, $validate->errors->first());
        }

        $params = $validate->requestData;

        DB::beginTransaction();
        try {
            $menu = Menu::find($params['id']);

            $menu->name  = $params['name'];
            $menu->pid   = $params['pid'];
            $menu->route = $params['pid'] == 0 ? null : $params['route'];
            $menu->save();

            //删除旧的关联关系
            MenuRoles::where('menu_id', '=', $params['id'])->delete();

            $pivot = [];
            foreach ($params['role'] as $role) {
                $pivot[] = [
                    'menu_id'    => $menu->id,
                    'roles_id'   => $role,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            MenuRoles::insert($pivot);

            DB::commit();
            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('菜单更新数据库异常', [$e->getMessage()]);
            return Response::response(Response::SQL_ERROR);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return Response::response(Response::PARAM_ERROR);
        }

        //初始化的菜单及子菜单不能被删除
        if ($id == 1) {
            return Response::response(Response::BAD_REQUEST, '当前菜单不能被删除');
        }

        $menu = Menu::find($id);
        if (!$menu || $menu->pid == 1) {
            return Response::response(Response::BAD_REQUEST, '当前菜单不能被删除');
        }

        $sub_count = Menu::where('pid', $id)->count();
        if ($sub_count > 0) {
            return Response::response(Response::BAD_REQUEST, '请先删除子菜单');
        }

        DB::beginTransaction();
        try {
            Menu::where('id', $id)->delete();
            MenuRoles::where('menu_id', $id)->delete();
            DB::commit();
            return Response::response();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('删除菜单数据库异常', [$e->getMessage()]);
            return Response::response(Response::SQL_ERROR);
        }
    }
}