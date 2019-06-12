<?php
/**
 * User: gedongdong@
 * Date: 2019/1/28 下午9:20
 */

namespace App\Validate;


use App\Http\Models\Menu;
use App\Http\Models\MenuRoles;
use App\Http\Models\Roles;
use App\Service\RouteService;

class MenuStoreValidate extends BaseValidate
{
    protected $rules = [];

    protected $message = [
        'name.unique'    => '名称已存在',
        'name.required'  => '请输入名称',
        'name.max'       => '名称最长20个字符',
        'route.required' => '请输入路由',
        'pid.required'   => '请选择父级菜单',
        'role.required'  => '请选择可见角色',
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->rules = [
            'name'  => 'required|unique:menu,name|max:20',
            'route' => 'nullable',
            'pid'   => 'required',
            'role'  => 'required',
        ];
    }

    protected function customValidate()
    {
        $pid   = $this->requestData['pid'];
        $route = $this->requestData['route'];
        $role  = $this->requestData['role'] ?? '';

        if ($pid < 0) {
            $this->validator->errors()->add('pid', '父级菜单参数不正确');
            return false;
        } elseif ($pid = 1) {
            $this->validator->errors()->add('pid', '不能在该菜单中添加子菜单');
            return false;
        } elseif ($pid > 1) {
            if (!Menu::find($pid)) {
                $this->validator->errors()->add('pid', '父级菜单不存在');
                return false;
            }
        }

        if ($pid != 0) {
            $routes = RouteService::getMenuRoutes();
            if (!in_array($route, $routes)) {
                $this->validator->errors()->add('route', '路由标识不存在');
                return false;
            }
        }

        if (!$role) {
            $this->validator->errors()->add('role', '请选择可见角色');
            return false;
        } else {
            if ($pid == 0) {
                if (count($role) != Roles::whereIn('id', $role)->count()) {
                    $this->validator->errors()->add('route', '可见角色参数不正确');
                    return false;
                }
            } else {
                if (count($role) != MenuRoles::where('menu_id', $pid)->whereIn('roles_id', $role)->count()) {
                    $this->validator->errors()->add('route', '可见角色参数不正确');
                    return false;
                }
            }
        }
    }
}