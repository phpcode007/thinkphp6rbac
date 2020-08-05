DROP TABLE IF EXISTS admin;
CREATE TABLE admin(
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  password char(32) NOT NULL DEFAULT '' COMMENT '密码',
  PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS role;
CREATE TABLE role(
  id int(11) NOT NULL AUTO_INCREMENT,
  role_name varchar(30) NOT NULL DEFAULT '' COMMENT '角色名称',
  PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS admin_role;
CREATE TABLE admin_role(
  id int(11) NOT NULL AUTO_INCREMENT,
  admin_id int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  role_id int(11) NOT NULL DEFAULT 0 COMMENT '角色ID',
  PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS rule;
CREATE TABLE rule(
  id int(11) NOT NULL AUTO_INCREMENT,
  rule_name varchar(30) NOT NULL DEFAULT '' COMMENT '权限名称',
  module_name varchar(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  controller_name varchar(30) NOT NULL DEFAULT '' COMMENT '控制器名称',
  action_name varchar(30) NOT NULL DEFAULT '' COMMENT '方法名称',
  parent_id int(11) not NULL DEFAULT 0 COMMENT '上级权限ID 0表示顶级权限',
  is_show tinyint(1) not NULL DEFAULT 1 COMMENT '是否导航菜单显示1  显示 0 不显示',
  PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS role_rule;
CREATE TABLE role_rule(
  id int(11) NOT NULL AUTO_INCREMENT,
  role_id int(11) NOT NULL DEFAULT 0 COMMENT '角色ID',
  rule_id int(11) NOT NULL DEFAULT 0 COMMENT '权限ID',
  PRIMARY KEY(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into admin values(null,'admin','e10adc3949ba59abbe56e057f20f883e');

insert into role values (null,'超级管理员');

insert into admin_role values (null,1,1);

insert into role_rule values (null,1,1);

insert into rule values (null,'角色管理','admin','role','#',0,1);
insert into rule values (null,'角色列表','admin','role','index',1,1);
insert into rule values (null,'添加角色','admin','role','add',1,1);
insert into rule values (null,'删除角色','admin','role','delete',1,0);

insert into rule values (null,'用户管理','admin','admin','#',0,1);
insert into rule values (null,'用户列表','admin','admin','index',5,1);
insert into rule values (null,'添加用户','admin','admin','add',5,1);
insert into rule values (null,'删除用户','admin','admin','delete',5,0);

insert into rule values (null,'权限管理','admin','rule','#',0,1);
insert into rule values (null,'权限列表','admin','rule','index',9,1);
insert into rule values (null,'添加权限','admin','rule','add',9,1);
insert into rule values (null,'删除权限','admin','rule','delete',9,0);