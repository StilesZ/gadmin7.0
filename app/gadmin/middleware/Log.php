<?php
/**
 *+------------------
 * Gadmin 3.0 企业级开发平台
 *+------------------
 * Copyright (c) 2021~2025 https://gadmin8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace app\gadmin\middleware;

use app\common\lib\Log as LogUtil;

use think\facade\Db;

/**
 *  日志后置中间件
 */
class Log
{

    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $args = LogUtil::getSysLog();
        if ($args['controller'] == 'Login' || $args['uri']=='/gadmin/smsg'|| $args['uri']=='/gadmin/index/pass.html'|| $args['uri']=='/gadmin/User/add.html'|| $args['uri']=='/gadmin/User/edit.html') {
            return $response;
        }
        if ($args['type'] == 'error') {
            Db::connect('db_log')->name('err')->insert($args);
        } else {
            Db::connect('db_log')->name('log')->insert($args);
        }
        return $response;
    }
}