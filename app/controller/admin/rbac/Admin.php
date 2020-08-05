<?php
namespace app\controller\admin\rbac;

use app\BaseController;
use app\validate\AdminCheck;
use think\facade\Db;
use app\exception\JsonApiException;
use think\facade\Session;

class Admin extends BaseController
{
    //用户列表
    public function index()
    {
        $sql = 'select a.id,a.username,c.role_name from admin a,admin_role b,role c where a.id=b.admin_id and b.role_id=c.id';
        $result = DB::query($sql);

        return view('admin/admin/index',['result'=>$result]);
    }

    //添加用户
    public function add()
    {
        if ($this->request->isGet()) {
            $sql = "select * from role";
            $result = Db::query($sql);

            return view('admin/admin/add',['result'=>$result]);
        } else {

            $username = $this->request->param('username');
            $password = $this->request->param('password');
            $role_id = $this->request->param('role_id');

            //验证参数
            validate(AdminCheck::class)
                ->scene('admin_add')
                ->check($this->request->param());

            // 启动事务
            Db::startTrans();

            try {
                $data = ['username' => $username, 'password' => md5($password)];
                $userid = Db::name('admin')->insertGetId($data);

                //定入用户角色中间表
                $sql = "insert into admin_role (admin_id,role_id) values (?,?)";
                Db::execute($sql, [$userid,$role_id]);

                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

            return 'ok';
        }
    }

    //删除用户
    public function delete()
    {
        validate(AdminCheck::class)
            ->scene('admin_delete')
            ->check($this->request->param());

        $user_id = $this->request->param('id');

        // 启动事务
        Db::startTrans();

        try {
            //第一步先删除用户表
            $sql = "delete from admin where id=?";
            Db::execute($sql,[$user_id]);

            //第二步删除用户角色中间表
            $sql = "delete from admin_role where admin_id=?";
            Db::execute($sql, [$user_id]);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }


        return redirect('/admin/admin/index');
    }

    //编辑用户
    public function edit()
    {
        validate(AdminCheck::class)
            ->scene('admin_edit')
            ->check($this->request->param());

        $user_id = $this->request->param('id');
        $username = $this->request->param('username');
        $password = $this->request->param('password');

        $role_id = $this->request->param('role_id');

        if ($this->request->isGet()) {

            //先查出这个id对应的角色名称
            $sql = 'select a.id,a.username,b.role_id from admin a,admin_role b where a.id=b.admin_id and a.id=?';
            $result = Db::query($sql, [$user_id]);

            //再查出所有的角色名称
            $sql = "select * from role";
            $role_result = Db::query($sql);

            return view('admin/admin/edit',['result'=>$result,'role_result'=>$role_result]);

        } else {

            //更新普通用户信息
            if (empty($password)) {
                $sql = "update admin set username=? where id=?";
                DB::execute($sql, [$username,$user_id]);

            } else {

                $sql = "update admin set username=?, password=? where id=?";
                DB::execute($sql, [$username,md5($password),$user_id]);
            }

            //更新用户和角色中间对应表
            $sql = 'update admin_role set role_id=? where admin_id=?';
            DB::execute($sql, [$role_id,$user_id]);

            return 'ok';
        }
    }

    //退出登录
    public function logout()
    {
        //删除用户名字session
        //清除session
        session(null);
//        Session::delete('user');
//        Session::delete('user_info');
        return redirect('/login');
    }
}
