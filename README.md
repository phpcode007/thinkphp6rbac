ThinkPHP 6.0 RBAC后台管理系统
===============

> 运行环境要求PHP7.1+。



## 主要特性

* 路由分组实现前台,后台,API等多应用模式
* 强制路由
* 中间件实现RBAC拦截验证
* 导航菜单实现
* 开启视图
* 开启Session
* 全局异常处理后返回JSON格式信息
* 验证不同场景的数据
* 开箱即用的RBAC后台管理系统,可以在此基础开发业务即可
* 可以当作THINKPHP 6.0 项目参考示例
* Layui后台


## Linux文档安装

进入项目的根目录
~~~
#1.先安装依赖库
composer install

#2.赋予缓存目录权限
chmod -R 777 runtime

#3.编辑config/database.php文件,配置连接mysql数据库,这时就不详细展开描述了

#4.导入sql/rbac.sql到对应数据库
~~~

后台账号admin 密码admin

后台地址
http://www.testtp.com/admin/index/index

admin/index/index  
路由格式是 模块/控制器/方法

对应目录是 app/controller/admin



![rbac图片](https://github.com/phpcode007/allphpcode/blob/master/public/img/rbac.png?raw=true)
