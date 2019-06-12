<?php
/**
 * User: gedongdong
 * Date: 2019/6/12 下午2:32
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