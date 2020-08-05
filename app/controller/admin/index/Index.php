<?php
namespace app\controller\admin\index;

use app\BaseController;
use think\View;
use think\facade\Db;
use think\facade\Session;

class Index extends BaseController
{
    public function index()
    {
        //从session拿出导航菜单
        $userinfo = session('user');

        return view('admin/index/index',['menus'=>$userinfo['menus']]);
    }

    //登录
    public function login()
    {
        if ($this->request->isGet()) {
            return view('admin/index/login');
        } else {
            $username = $this->request->param('username');
            $password = $this->request->param('password');

            $user_info = Db::query("select * from admin where username=? and password=?", [$username,md5($password)]);


            if (empty($user_info)) {
                return '用户名或密码错误';
            } else {
                //写入用户名session
//                session('username', $username);
                //写入用户信息,先删除密码后再存入session
                unset($user_info[0]['password']);

                session('user_info', $user_info);

                return 'ok';
            }

        }
    }

}
