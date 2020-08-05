<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;


//后台路由分组,已经开启完全路由匹配成功才可以访问
Route::group('admin', function () {
    //后台首页
    Route::any('index/index', 'admin.index.Index/index');
    //退出后台
    Route::any('admin/logout', 'admin.rbac.Admin/logout');

    //角色列表
    Route::any('role/index', 'admin.rbac.Role/index');
    //添加角色
    Route::any('role/add', 'admin.rbac.Role/add');
    //删除角色
    Route::any('role/delete', 'admin.rbac.Role/delete');
    //编辑角色
    Route::any('role/edit', 'admin.rbac.Role/edit');


    //用户列表
    Route::any('admin/index', 'admin.rbac.Admin/index');
    //添加用户
    Route::any('admin/add', 'admin.rbac.Admin/add');
    //删除用户
    Route::any('admin/delete', 'admin.rbac.Admin/delete');
    //编辑用户
    Route::any('admin/edit', 'admin.rbac.Admin/edit');


    //权限列表
    Route::any('rule/index', 'admin.rbac.Rule/index');
    //添加权限
    Route::any('rule/add', 'admin.rbac.Rule/add');
    //删除权限
    Route::any('rule/delete', 'admin.rbac.Rule/delete');
    //编辑权限
    Route::any('rule/edit', 'admin.rbac.Rule/edit');
    //赋予权限
    Route::any('rule/disfetch', 'admin.rbac.Rule/disfetch');

    //获取树状菜单
    Route::any('rule/gettreejson', 'admin.rbac.Rule/gettreejson');

//});
})->middleware(\app\middleware\CheckAuth::class);
