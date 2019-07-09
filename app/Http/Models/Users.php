<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    protected $guarded = [];

    const STATUS_ENABLE = 1;

    const STATUS_DISABLE = 2;

    const ADMIN_YES = 1;

    const ADMIN_NO = 2;

    public function roles()
    {
        return $this->belongsToMany('App\Http\Models\Roles', 'users_roles');
    }
}
