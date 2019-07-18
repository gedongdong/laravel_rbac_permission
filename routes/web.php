<?php

/*
 * This file is part of the gedongdong/laravel_rbac_permission.
 *
 * (c) gedongdong <gedongdong2010@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

//以.white结尾的别名为不需要授权的路由

Route::namespace('Admin')->prefix('admin')->group(function () {
    Route::get('login', 'LoginController@index')->name('admin.login.white');
    Route::post('login', 'LoginController@login')->name('admin.login.post.white');
    Route::post('logout', 'LoginController@logout')->name('admin.logout.white');

    Route::middleware(['login', 'menu'])->group(function () {
        Route::get('/', 'AdminController@index')->name('admin.index.white');
        Route::get('modify_pwd', 'AdminController@modifyPwd')->name('admin.modify_pwd.white');
        Route::post('new_pwd', 'AdminController@newPwd')->name('admin.new_pwd.white');
        Route::get('forbidden', function () {
            return view('admin.403');
        })->name('admin.forbidden.white');

        Route::middleware('auth.can')->group(function () {
            Route::get('/user', 'UserController@index')->name('admin.user.index');
            Route::get('/user/create', 'UserController@create')->name('admin.user.create');
            Route::post('/user/store', 'UserController@store')->name('admin.user.store');
            Route::post('/user/status', 'UserController@status')->name('admin.user.status');
            Route::get('/user/edit', 'UserController@edit')->name('admin.user.edit');
            Route::post('/user/update', 'UserController@update')->name('admin.user.update');
            Route::post('/user/reset', 'UserController@reset')->name('admin.user.reset');

            Route::get('/permission', 'PermissionController@index')->name('admin.permission.index');
            Route::get('/permission/create', 'PermissionController@create')->name('admin.permission.create');
            Route::post('/permission/store', 'PermissionController@store')->name('admin.permission.store');
            Route::get('/permission/edit', 'PermissionController@edit')->name('admin.permission.edit');
            Route::post('/permission/update', 'PermissionController@update')->name('admin.permission.update');
            Route::post('/permission/delete', 'PermissionController@delete')->name('admin.permission.delete');

            Route::get('/roles', 'RolesController@index')->name('admin.roles.index');
            Route::get('/roles/create', 'RolesController@create')->name('admin.roles.create');
            Route::post('/roles/store', 'RolesController@store')->name('admin.roles.store');
            Route::get('/roles/edit', 'RolesController@edit')->name('admin.roles.edit');
            Route::post('/roles/update', 'RolesController@update')->name('admin.roles.update');
            Route::post('/roles/delete', 'RolesController@delete')->name('admin.roles.delete');

            Route::get('/menu', 'MenuController@index')->name('admin.menu.index');
            Route::get('/menu/create', 'MenuController@create')->name('admin.menu.create');
            Route::post('/menu/store', 'MenuController@store')->name('admin.menu.store');
            Route::get('/menu/edit', 'MenuController@edit')->name('admin.menu.edit');
            Route::post('/menu/update', 'MenuController@update')->name('admin.menu.update');
            Route::post('/menu/delete', 'MenuController@delete')->name('admin.menu.delete');

            Route::get('/test1', 'TestController@test1')->name('admin.test1.index');
            Route::get('/test2', 'TestController@test2')->name('admin.test2.index');
            Route::get('/test3', 'TestController@test3')->name('admin.test3.index');
            Route::get('/test4', 'TestController@test4')->name('admin.test4.index');
            Route::get('/test5', 'TestController@test5')->name('admin.test5.index');
        });
    });
});
