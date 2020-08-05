<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Session;
use think\facade\Db;
use think\facade\Request;

class CheckAuth
{
    /**
     * 用中间件处理后台验证session
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {

        //标识是否进行权限认证
        $is_check_rule = true;
        //保存用户的信息，包括基本信息，角色ID,权限信息
        $user = [];

        //如果值不存在，返回空字符串
        $session_user_info = Session::get('user_info', '');

        //如果没有登录过,重定向到登录页面
        if (empty($session_user_info)) {
            return redirect('/login');
        }

        //获取保存用户的session信息
        $check_has_session = session('user');

        $get_user_info = session('user_info');

        if ($check_has_session) {

            //已经有缓存，不再从数据库读取,减少数据库压力
            //可以做一个功能，一键清除用户session和缓存信息
            if ($check_has_session['role_id'] != 1) {
                //获取当前用户访问的url地址
                $action = strtolower(Request::pathinfo());

                if (!in_array($action, $check_has_session['rules'])) {
                    //不是管理员,直接跳到登录页需要清除所有session
                    //这样其它用户登录才不会发生session错乱问题
                    session(null);
                    return redirect('/login');
                }
            }

            return $next($request);
        }

        //将当前用户的信息保存到属性中
        $user[] = $session_user_info[0];

        //根据用户ID获取对应的角色ID
        $role_info = Db::query('select role_id from admin_role where admin_id = ?',[$get_user_info[0]['id']]);

        $role_id = $role_info[0]['role_id'];


        $user['role_id'] = $role_id;


        if ($role_id == 1) {
            //超级管理员
            //不验证权限
            $is_check_rule = false;
            //虽然不用验证，但是也需要导航菜单，获取所有权限信息
            $rule_list = Db::query('select * from rule');

        } else {
            //普通用户
            //1.根据角色ID获取对应的权限ID
            $rules = Db::query('select * from role_rule where role_id=?',[$role_id]);

            //保存权限ID,保存成一维数组，用in查询
            foreach ($rules as $key => $value) {
                $rules_ids[] = $value['rule_id'];
            }

            //转化为,分隔
            $rules_ids = implode(',', $rules_ids);

            $sql = "select * from rule where id in ($rules_ids)";
            //根据权限ID获取对应的权限信息
            $rule_list =  Db::query($sql);
        }

        //将用户对应的二维数组的权限信息转换为一维，并保存到user session中
        foreach ($rule_list as $key => $value) {
            //将对应的模型，控制器，方法的名称进行拼接
            $user['rules'][] = strtolower($value['module_name'] . '/' . $value['controller_name'] . '/' . $value['action_name']);
            //考虑到导航菜单的显示
            if ($value['is_show'] == 1) {
                $user['menus'][] = $value;
            }

        }


        //对权限进行验证
        if ($is_check_rule) {
            //普通用户
            //增加后台首页访问权限
            $user['rules'][] = 'admin/index/index';

            //获取当前用户访问的url地址
            $action = strtolower(Request::pathinfo());

            if (!in_array($action, $user['rules'])) {
                session(null);
                return redirect('/login');
            }
        }



        //保存用户的session信息
        session('user', $user);

        return $next($request);
    }
}
