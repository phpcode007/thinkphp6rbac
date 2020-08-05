<?php
namespace app\controller\admin\rbac;

use app\BaseController;
use app\validate\AdminCheck;
use think\facade\Db;
use app\exception\JsonApiException;

class Role extends BaseController
{
    //角色列表
    public function index()
    {
        if ($this->request->isGet()) {

            $sql = "select * from role";
            $result = Db::query($sql);

            return view('admin/role/index',['result'=>$result,'name'=>'xjq']);
        } else {

        }
    }

    //添加角色
    public function add()
    {
        if ($this->request->isGet()) {
            return view('admin/role/add');
        } else {

            $role_name = $this->request->param('role_name');

            //验证参数
            validate(AdminCheck::class)
                ->scene('role_add')
                ->check($this->request->param());

            $sql = "insert into role (role_name) values (?)";
            Db::execute($sql, [$role_name]);

            return 'ok';


        }
    }

    //删除角色
    public function delete()
    {
        validate(AdminCheck::class)
            ->scene('role_delete')
            ->check($this->request->param());

        $role_id = $this->request->param('role_id');

        if ($role_id <= 1) {
            throw new JsonApiException(0,'超级管理员不能删除');
        }

        $sql = "delete from role where id = ?";
        Db::execute($sql, [$role_id]);

        return redirect('/admin/role/index');
    }

    //编辑角色
    public function edit()
    {
        validate(AdminCheck::class)
            ->scene('role_edit')
            ->check($this->request->param());

        $role_id = $this->request->param('role_id');

        if ($role_id <= 1) {
            throw new JsonApiException(0,'超级管理员不能编辑');
        }

        if ($this->request->isGet()) {

            $sql = "select * from role where id=?";
            $result = Db::query($sql, [$role_id]);

            return view('admin/role/edit',['result'=>$result]);

        } else {
            $role_name = $this->request->param('role_name');

            $sql = "update role set role_name = ? where id=$role_id";
            Db::query($sql, [$role_name]);

            return 'ok';
        }
    }


}
