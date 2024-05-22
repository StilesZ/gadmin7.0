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

namespace app\gadmin\controller;


use think\facade\Db;
use think\facade\Request;

class Msg extends Base
{
    public function index($map = [])
    {
        if (Request::isPost()) {
            if (input("title")) $map[] = ['title', 'like', '%'.input('title').'%'];
            if (input("con")) $map[] = ['content', 'like', '%'.input('con').'%'];
            if (input("type")==0){
                $map[] = ['is_read', '=', 0];
            }else{
                $map[] = ['is_read', '=', 1];
            }
            $data = \app\common\server\Msg::mList(['uid'=>session('softId')],input('limit'),input('page'),$map);
            return json($data);
        }
        return view();
    }
    public function msg()
    {
        return view('msg', ['list' => \app\common\server\Msg::mList(['uid'=>session('softId'),'is_read'=>0])]);
    }
    public function read_id($id){
        $id = input('id');
        $ret = \app\common\server\Msg::mRead(['id'=>$id]);
        return json($ret);
    }

    public function msg_set(){
        $action = input('act');
        if($action=='read'){
            $ret = \app\common\server\Msg::mRead(['uid'=>session('softId')]);
        }
        if($action=='del'){
            $ret =  \app\common\server\Msg::mDel(['uid'=>session('softId')]);
        }
        return json($ret);
    }
}
