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

class Menu extends Model
{
    protected $table = 'menu';

    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany('App\Http\Models\Roles', 'menu_roles');
    }

    public static function menuTree(&$all_meuns, &$tree)
    {
        foreach ($all_meuns as $key => $menu) {
            if (isset($all_meuns[$menu['pid']])) {
                $all_meuns[$menu['pid']]['children'][] = &$all_meuns[$key];
            } else {
                $tree[] = &$all_meuns[$menu['id']];
            }
        }
    }
}
