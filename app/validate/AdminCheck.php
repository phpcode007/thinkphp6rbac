<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class AdminCheck extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        //公共id主键数字
        'id' => 'require|checkID',

        //admin/role/add
        'role_name' => 'require|unique:role',

        //admin/role/delete
        //admin/role/edit
        'role_id' => 'require|checkID',

        //admin/admin/add
        'username' => 'require|unique:admin',
        'password' => 'require',


        //admin/rule/add
        'rule_name' => 'required',
        'module_name' => 'required|alpha',
        'controller_name' => 'required|alpha',
        'action_name' => 'required|alpha',
        'isshow' => 'required|integer',
        'parent_id' => 'required|integer'

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        //公共id
        'id.require' => ['code' => 10000, 'msg' => 'id必须填写'],
        'id.checkID' => ['code' => 10000, 'msg' => 'id必须是正整数'],

        //admin/admin/add
        'role_name.require' => ['code' => 10000, 'msg' => '角色名称必须填写'],
        'role_name.unique' => ['code' => 10000, 'msg' => '角色名称已经存在,请输入其它角色名称'],

        //admin/admin/delete
        //admin/admin/edit
        'role_id' => ['code' => 10000, 'msg' => '角色id参数错误'],

        //admin/admin/add
        'username.require' => ['code' => 10000, 'msg' => '用户名必须填写'],
        'username.unique' => ['code' => 10000, 'msg' => '用户名称已经存在,请输入其它用户名称'],
        'password.require' => ['code' => 10000, 'msg' => '密码必须填写'],
    ];

    /**
     *  验证场景
     */
    protected $scene = [
        //admin/admin/add
        'role_add' => ['role_name'],

        //admin/admin/delete
        'role_delete' => ['role_id'],

        //admin/admin/edit
        'role_edit' => ['role_id'],

        //admin/amdin/add
        'admin_add' => ['username','password','role_id'],

        //admin/admin/delete
        'admin_delete' => ['id'],

        //admin/admin/edit
        'admin_edit' => ['id'],

        //amin/rule/add
        'rule_add' => ['rule_name','module_name','controller_name','action_name','isshow','parent_id'],
    ];

    // 自定义正整数验证规则
    protected function checkID($value, $rule)
    {
        if(preg_match('/^[1-9]\d*$/',$value)) {
            return true;
        } else {
            return false;
        }
    }
}