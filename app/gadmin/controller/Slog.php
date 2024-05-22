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

class Slog extends Base
{
    //登入日志系统
    public function login($page = 1, $limit = 15, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
            if (input("username")) $map[] = ['username', 'like', '%'.input('username').'%'];
            $List = self::listData('login', $map, $page, $limit);
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $List['data']]);
        }
        return view('login');
    }

    //消息日志系统
    public function info($page = 1, $limit = 15, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
			if (input("type")) $map[] = ['type', 'like', '%'.input('type').'%'];
			if (input("uri")) $map[] = ['uri', 'like', '%'.input('uri').'%'];
            $List = self::listData('log', $map, $page, $limit);
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $List['data']]);
        }
        return view('info');
    }

    //错误日志系统
    public function err($page = 1, $limit = 15, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
            if (input("type")) $map[] = ['type', 'like', '%'.input('type').'%'];
			if (input("uri")) $map[] = ['uri', 'like', '%'.input('uri').'%'];
            $List = self::listData('err', $map, $page, $limit);
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $List['data']]);
        }
        return view('err');
    }
    public function view($type,$id){
    	if($type=='err'){
			$find = Db::connect('db_log')->name('err')->find($id);
		}
		if($type=='info'){
			$find = Db::connect('db_log')->name('log')->find($id);
		}
		if($type=='login'){
			$find = Db::connect('db_log')->name('login')->find($id);
			$find['sql'] = '[]';
		}
		return view('view',['row'=>$find,'type'=>$type]);
	}
	
    //列表数据获取
    static function listData($table, $map = [], $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $list = Db::connect('db_log')->name($table)->where($map)->limit($offset, $limit)->order('id desc')->select()->toArray();
        $jsondata = [];
        foreach ($list as $k => $v) {
            if ($table == 'login') {
                $list[$k]['login_time'] = date('Y-m-d H:i:s', $v['login_time']);
            }
            $jsondata[$k] = array_values($list[$k]);
        }
        $count = Db::connect('db_log')->name($table)->where($map)->count();
        return ['data' => $jsondata, 'count' => $count];
    }

    //数据清空动作
    public function del($table)
    {
        $ret = Db::connect('db_log')->name($table)->delete(true);
        if ($ret) {
            return msg_return('删除成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }
}
