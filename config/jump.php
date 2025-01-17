<?php
/**
 * Created by PhpStorm.
 * User: liliuwei
 * Date: 2019/5/23
 * Time: 22:50
 */

declare(strict_types=1);
return [
    // 成功跳转页面模板文件
    'dispatch_success_tpl'  => app()->getRootPath().'/view/gadmin/'.g_cache('view').'/jump.tpl',
    // 成功跳转页停留时间(秒)
    'default_success_wait' => 3,
    // 成功跳转 code 值
    'default_success_code'  => 1,
    // 错误跳转页面模板文件
    'dispatch_error_tpl'    => app()->getRootPath().'/view/gadmin/'.g_cache('view').'/jump.tpl',
    // 错误跳转页停留时间(秒)
    'default_error_wait'   => 3,
    // 错误跳转 code 值
    'default_error_code'    => 0,
    // 默认输出类型
    'default_return_type'   => 'html',
    // 默认 AJAX 请求返回数据格式，可用：Json,Jsonp,Xml
    'default_ajax_return'   => 'json',
];