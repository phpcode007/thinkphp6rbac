<?php

namespace app\controller\admin\rbac;

use app\BaseController;
use app\validate\AdminCheck;
use think\facade\Db;

class Rule extends BaseController
{

    public function index()
    {
        return view('admin/rule/index');
    }


    public function add()
    {
        if($this->request->isGet()){
            return view('admin/rule/add');
        } else {

            $rule_name = $this->request->param('rule_name');
            $module_name = $this->request->param('module_name');
            $controller_name = $this->request->param('controller_name');
            $action_name = $this->request->param('action_name');
            $is_show = $this->request->param('is_show');
            $parent_id = $this->request->param('parent_id');

            //写入rule表
            $sql = 'insert into rule (rule_name,module_name,controller_name,action_name,is_show,parent_id) values (?,?,?,?,?,?)';
            Db::execute($sql, [$rule_name,$module_name,$controller_name,$action_name,$is_show,$parent_id]);

            return ['ok'];
        }
    }

    public function edit()
    {
        $rule_id = $this->request->param('rule_id');

        if($this->request->isGet()){

            //先查出这个id对应的角色名称
            $rule_result = Db::query('select * from rule where id= ?',[$rule_id]);

            //加载模板
            return view('admin/rule/edit',['result'=>$rule_result]);

        } else {


            //需要过滤掉设置父分类为自己或自己下的子分类
            //1.根据要修改的分类标识，获取到自己下的所有子分类
            //2.根据提交的parent_id的值
            //  判断如果等于当前修改的分类ID或者是当前分类下的所有子类的ID,不允许修改

            $rule_name = $this->request->param('rule_name');
            $module_name = $this->request->param('module_name');
            $controller_name = $this->request->param('controller_name');
            $action_name = $this->request->param('action_name');
            $parent_id = $this->request->param('parent_id');
            $is_show = $this->request->param('is_show');

            $rule_id = $this->request->param('rule_id');

            //1.这里获取到这个权限ID下面还有没有子分类
            $tree = getRuleTree($rule_id);

            //2.还需要将自己的分类ID添加到不能修改的数组中
            $tree[] = ['id' => $rule_id];

            foreach ($tree as $value) {

                if ($parent_id == $value['id']) {
                    return '不能修改自己为父分类，或把自己修改到子分类下面';
                }

            }

            $sql = 'update rule set rule_name=?,module_name=?,controller_name=?,action_name=?,parent_id=?,is_show=? where id=?';

            DB::execute($sql, [$rule_name,$module_name,$controller_name,$action_name,$parent_id,$is_show,$rule_id]);

            return 'ok';
        }
    }

    public function delete()
    {
        $rule_id = $this->request->param('rule_id');

        //如果删除的权限下还有子权限，不能删除
        $result = Db::query('select id from rule where parent_id=?',[$rule_id]);
        if ($result) {
            return '删除的权限下还有子权限，不能删除，请先删除子权限之后再删除父权限';
        }

        $roles_data = DB::execute('delete from rule where id = ?',[$rule_id]);

        return '删除权限成功';
    }

    //赋予权限
    public function disfetch()
    {
        $role_id = $this->request->param('role_id');

        if($this->request->isGet()) {

            //获取当前角色拥有的权限信息
            $rules = Db::query('select * from role_rule where role_id = ?',[$role_id]);
            $rules = array_column($rules, 'rule_id');

            $hasRules=implode(',', $rules);

            //拼接一个字符串给tree使用
            $hasRules = "[" . $hasRules . "]";

            return view('admin/rule/disfetch',['hasRules'=>$hasRules]);
        } else {

            //对超级管理员不需要赋予权限
            if ($role_id <= 1) {
                return;
            }

            $ruleids = $this->request->param('ruleids');

            if (empty($ruleids)) {
                return redirect('/admin/rule/index');
            }

            $rulearray = explode(',', $ruleids);

            //将当前角色对应的权限删除
            Db::execute('delete from role_rule where role_id = ?',[$role_id]);
            //将最新的权限写入数据库
            //这个操作频率很低，可以使用循环插入
            foreach ($rulearray as $v) {
                Db::execute('insert into role_rule (role_id,rule_id) values (?,?)',[$role_id,$v]);
            }

            return 'ok';

        }

    }


    //获取树状菜单,返回json字符串
    public function gettreejson()
    {
        $treetype = $this->request->param('type');

        $result = getMenuList($treetype);
        return json($result);
    }


}




//获取权限数据，更新和查询树形使用
function getRuleTree($id=0)
{
    $data = Db::query('select * from rule');

    //这个函数递归使用,单独抽出来
    $list = getTree($data,$id);

    return $list;
}


//格式化权限信息
//递归时id默认是0,之后每一次调用都需要改变
//第一次层级是1,之后每一次递归都需要递增
function getTree($data,$id=0,$lev=1)
{
    //每次递归这个都会变,用static固定
    static $list = [];

    foreach ($data as $key => $value) {

        if ($value['parent_id'] == $id) {
            //由于没有层次,也看不出来层级，需要添加一个参数来增加层级显示
            $value['lev'] = $lev;

            $list[] = $value;
            //使用递归
            //之后每一次调用都需要改变
            getTree($data,$value['id'],$lev+1);

        }
    }

    return $list;
}





// 获取菜单列表
function getMenuList($treetype){

    //使用layuitree和treeSelect树形下拉选择器
    //这两种树返回的json字段中,名字不一样，需要区分一下
    if ($treetype == 'treeselect') {
        $sql = "select id,rule_name as name,parent_id from rule";
    } else {
        $sql = "select id,rule_name as title,parent_id from rule";
    }

    $menuList = Db::query($sql);

    $menuList = buildMenuChild(0, $menuList);

    return $menuList;
}

//递归获取子菜单
function buildMenuChild($pid, $menuList){
    $treeList = [];
    foreach ($menuList as $v) {

        if ($pid == $v['parent_id']) {
            $node = (array)$v;

            $child = buildMenuChild($v['id'], $menuList);
            if (!empty($child)) {
                $node['children'] = $child;
            }

            //增加一个href跳转属性
            $node['href'] = "/admin/rule/edit?rule_id=" .$v['id'];

            $treeList[] = $node;
        }
    }
    return $treeList;
}



