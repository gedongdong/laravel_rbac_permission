<?php
function can()
{
    $currRouteName = \Illuminate\Support\Facades\Route::currentRouteName();

    if (ends_with($currRouteName, 'white')) {
        return true;
    }

    $user = session()->get('user');
    if ($user['administrator'] == \App\Http\Models\Users::ADMIN_YES) {
        return true;
    }

    $role_ids = \App\Http\Models\UsersRoles::where('users_id', $user['id'])->pluck('roles_id')->toArray();

    $permission_ids = \App\Http\Models\RolePermission::whereIn('roles_id', $role_ids)->pluck('permission_id')->toArray();

    $check = \App\Http\Models\Permission::whereIn('id', $permission_ids)->where('routes', 'like', "%{$currRouteName}%")->count();
    if ($check > 0) {
        return true;
    }
    return false;
}