<?php
/**
 *+------------------
 * fkreport
 *+------------------
 */
return [
	'db_namespace'=>'',
	'db_mode'=>1,
    'int_user_name'=> 'sfotUserName',//定义用户名称
    'int_user_id'=> 'softId',//定义用户id
    'int_user_role'=> 'sfotRoleId',//定义用户角色
    'int_url'=>'/gadmin',//使用工作流的模块名称
	'upload_file' => '/gadmin/report/upload',//附件上传接口
    'url_api' => '/gadmin/report/sapi',
    'fun_mode' => 2,//1、系统模式  2、二次开发模式
    'fun_namespace' =>'\\sys\\SfdpFun',//自定义方法返回数据 命名空间 中的GetUserInfo
];
