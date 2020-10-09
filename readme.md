# Laravel RBAC Permission Admin

基于Laravel框架，前端采用Layui组件（基于Jquery），包含通用RBAC权限的后台管理系统。

Demo： http://rbac.elnmp.com/admin

user name：admin@admin.com

password：admin123

## 环境要求

* PHP >= 7.0
* 该分支支持 Laravel 7.x
* Laravel  5.5.* / 6.x 支持：https://github.com/gedongdong/laravel_rbac_permission/tree/master
* Laravel  8.x 支持：https://github.com/gedongdong/laravel_rbac_permission/tree/laravel-8.x

## 基础功能

* 登录/登出
* 登录验证码
* 用户管理
* 角色管理
* 权限组管理
* 基于角色的菜单管理
* 密码修改

## 项目初始化

1. 将项目根目录的rbac.sql文件导入数据库
2. 配置nginx/apache
3. 拉取代码，再`composer install`
4. 由于涉及到初始超管用户密码加密的问题，先使用`.env.example`中的`APP_KEY`进行登录，然后再生成新的`APP_KEY`，重置超管密码

## 效果展示

![](http://docimg.elnmp.com/login.png)
![](http://docimg.elnmp.com/role_add.png)
![](http://docimg.elnmp.com/menu_add.png)
![](http://docimg.elnmp.com/role.png)
![](http://docimg.elnmp.com/user.png)
![](http://docimg.elnmp.com/user_add.png)
![](http://docimg.elnmp.com/permission.png)
![](http://docimg.elnmp.com/permission_add.png)
![](http://docimg.elnmp.com/menu.png)
![](http://docimg.elnmp.com/newpwd.png)
