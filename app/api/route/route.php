<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
use think\facade\Db;

Route::rule(':version/getConfig',':version.Common/config'); //用户登入

Route::rule(':version/login',':version.Common/index'); //用户登入

Route::rule(':version/getDesk',':version.Common/getDesk'); //获取桌面信息

Route::rule(':version/getTxl',':version.Common/getTxl'); //获取通讯录信息

Route::rule(':version/getLogin',':version.Common/getLogin'); //获取通讯录信息

Route::rule(':version/getMsg',':version.Common/getMsg'); //获取我的消息

Route::rule(':version/readMsg',':version.Common/readMsg'); //设置我的消息为已读

Route::rule(':version/getSchedule',':version.Common/getSchedule'); //设置我的消息为已读

Route::rule(':version/saveSchedule',':version.Common/saveSchedule'); //设置我的消息为已读

Route::rule(':version/userChange',':version.Common/userChange'); //用户修改密码

Route::rule(':version/getApp',':version.Common/getApp'); //获取应用数据

Route::rule(':version/fileUploads',':version.Common/uploads'); //多文件上传

Route::rule(':version/fileUp',':version.Common/uploding'); //单文件上传

Route::rule(':version/fileDel',':version.Common/filedel'); //附件删除

Route::rule(':version/open_data',':version.Common/open_data'); //增加外部接口获取数据

Route::rule(':version/getSysApp',':version.Sys/getData'); //获取系统数据

Route::rule(':version/getSysCon',':version.Sys/getSysCon'); //获取系统详细信息

Route::rule(':version/getYwApp',':version.App/getData'); //获取业务信息

Route::rule(':version/getYwCon',':version.App/getSysCon'); //获取业务内容

Route::rule(':version/getWork',':version.App/getWork'); //获取工作流信息

Route::rule(':version/wfcheck',':version.App/wfcheck'); //审核审批

Route::rule(':version/show',':version.App/show'); //审核审批

Route::rule(':version/sfdp_index',':version.Sfdp/index'); //数据列表
Route::rule(':version/sfdp_init',':version.Sfdp/sinit'); //接口数据

Route::rule(':version/sfdp_status',':version.Sfdp/status'); //修改
Route::rule(':version/sfdp_send',':version.Sfdp/send'); //修改
Route::rule(':version/sfdp_del',':version.Sfdp/del'); //修改
Route::rule(':version/sfdp_edit',':version.Sfdp/edit'); //修改
Route::rule(':version/sfdp_add',':version.Sfdp/add'); //添加
Route::rule(':version/sfdp_view',':version.Sfdp/view'); //审核审批

Route::rule(':version/sfdp_sapi',':version.Sfdp/sapi'); //元素内部调用接口

$cate = Db::name('sfdp_data')->field('id,table,route,type')->select()->toArray();
foreach ($cate as $k => $v) {
    if($v['type']==1){
        Route::rule('m/'.$v['route'],'m.'.$v['table'].'/index');
        Route::rule('m/'.$v['route'].'_pass','m.'.$v['table'].'/pass');
        }else{
        Route::rule('m/'.$v['route'],'m.'.$v['table'].'/index');
        Route::rule('m/'.$v['route'].'_pass','m.'.$v['table'].'/pass');
    }
}

