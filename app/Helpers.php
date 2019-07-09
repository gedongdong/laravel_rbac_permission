<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

function can()
{
    $currRouteName = \Illuminate\Support\Facades\Route::currentRouteName();

    if (ends_with($currRouteName, '.white')) {
        return true;
    }

    $user = session()->get('user');
    if (\App\Http\Models\Users::ADMIN_YES == $user['administrator']) {
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

if (!function_exists('ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    function ends_with($haystack, $needles)
    {
        return \Illuminate\Support\Str::endsWith($haystack, $needles);
    }
}
