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

class TestController extends Controller
{
    public function test1()
    {
        return view('admin.test', ['content' => '这是test1的测试']);
    }

    public function test2()
    {
        return view('admin.test', ['content' => '这是test2的测试']);
    }

    public function test3()
    {
        return view('admin.test', ['content' => '这是test3的测试']);
    }

    public function test4()
    {
        return view('admin.test', ['content' => '这是test4的测试']);
    }

    public function test5()
    {
        return view('admin.test', ['content' => '这是test5的测试']);
    }
}
