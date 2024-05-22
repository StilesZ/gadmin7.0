<?php

namespace sys;

use app\gadmin\controller\Source;
use think\facade\Db;

/**
 *+------------------
 * Gadmin 企业级开发平台
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 */
class SfdpFun {

    public function func($fun,$act=''){
        $app = app('http')->getName();
        $Source = app('app\gadmin\controller\Source');
        //读取缓存方法
        if(strstr($fun,'#')){
            $keys = explode('&',$fun);
            $info = g_cache('sp_dictionary_key');
            $op_data = $info[$keys[1]];
            $op = [];
            foreach ($op_data as $k=>$v2){
                $op[] = ['id'=>$v2['detail_value'],'name'=>$v2['detail_name']];
            }
            return ['code'=>0,'msg'=>$op];
        }
        if($app =='api'){
            //判断是否是开放数据模块
            if(strpos(Request()->controller(),'m.') !== false){
                $info = Db::name('sfdp_data')->whereFindInSet('source',$fun)->find();
                if($info){
                    $data =  $Source->api($fun,$act,0,['uid'=>0,'role'=>0]);
                }
            }else{
                $oauth = app('app\api\controller\Oauth');
                $userinfo =  $oauth->authenticate();;
                $data =  $Source->api($fun,$act,0,['uid'=>$userinfo['uid'],'role'=>$userinfo['role']]);
            }

        }
        if($app =='gadmin'){
            $data =  $Source->api($fun,$act);
        }
        $ret = json_decode($data->getContent(),true);
        if($ret['code'] == 1){
            return ['code'=>-1,'msg'=>'Sorry，[未找到自定函数，请先配置]'];
        }
        if($ret['code'] == -1){
            return ['code'=>-1,'msg'=>$ret['msg']];
        }else{
            return ['code'=>0,'msg'=>$ret['data']];
        }
    }
}
?>