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
use Tree;

class Access extends Base
{

    /**
     *权限列表
     */
    public function index()
    {
        $tree = Db::name('softRole')->where('id', '>', 1)->field('id,name,pid,name as title')->select()->toArray();
        $user = Db::name('softUser')->where('id', '>', 1)->field('id,username,realname')->select()->toArray();
        return view('index', ['data' => json_encode(g_generateTree($tree)),'user'=>$user]);
    }

    /**
     *数据获取
     */
    public function data($roleid = '')
    {
        if ($roleid == '') {
            return '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>(●◡●)</h1>请选择需要授权的角色/用户！</h2>';exit;
        }
        $access = obj2arr(Db::name('softAccess')->field('role_id,node_id,pid,level')->select());
        $data2 = Db::name('softNode')->field('id,title,title as label,pid,level')->order('sort asc')->select()->toArray();
        foreach ($data2 as $n => $t) {
            if($this->is_checked($t, $roleid, $access)){
                $data2[$n]['checkArr'] =["type"=>"0", "checked"=>"1"];
            }else{
                $data2[$n]['checkArr'] =["type"=>"0", "checked"=>"0"];
            }
        }
        return view('data', ['data3' => json_encode(g_generateTree($data2)),'roleid' => $roleid]);
    }
    /**
     *数据获取
     */
    public function data2($userid = '')
    {
        if ($userid == '') {
            return '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>(●◡●)</h1>请选择需要授权的角色/用户！</h2>';exit;
        }
        $access = obj2arr(Db::name('softAccessUid')->field('uid_id,node_id,pid,level')->select());
        $data2 = Db::name('softNode')->field('id,title,title as label,pid,level')->order('sort asc')->select()->toArray();
        foreach ($data2 as $n => $t) {
            if($this->is_checked($t, $userid, $access,'uid_id')){
                $data2[$n]['checkArr'] =["type"=>"0", "checked"=>"1"];
            }else{
                $data2[$n]['checkArr'] =["type"=>"0", "checked"=>"0"];
            }
        }
        return view('data2', ['data3' => json_encode(g_generateTree($data2)),'roleid' => $userid]);
    }
    /**
     *是否选中
     */
    public function is_checked($node, $roleid, $access,$id='role_id')
    {
        $nodetemp = [
            $id => $roleid,
            'node_id' => $node['id'],
            'pid' => $node['pid'],
            'level' => $node['level']
        ];
        $info = in_array($nodetemp, $access);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *权限修改
     */
    public function access_edit()
    {
        $roleid = input('roleid');
        $nodeid = explode(',',input('ids')) ?? '';
        if (is_array($nodeid) && count($nodeid) > 0) {  //提交得有数据，则修改原权限配置
            Db::name('softAccess')->where('role_id', $roleid)->delete();
            $node = Db::name('softNode')->order('sort asc')->select();
            foreach ($node as $_v) $node[$_v['id']] = $_v;
            foreach ($nodeid as $k => $node_id) {
                $data[$k] = $this->get_nodeinfo($node_id, $node);
                $data[$k]['role_id'] = $roleid;
            }
            $ret = Db::name('softAccess')->insertAll($data);
        } else {
            $ret = Db::name('softAccess')->where('role_id', $roleid)->delete();
        }
        if ($ret) {
            return msg_return('成功！');
        } else {
            return msg_return('删除失败,该用户所有权限已被清空！', 1);
        }
    }

    /**
     *权限修改
     */
    public function access_edit2()
    {
        $datas = $this->request->post();
        $roleid = input('roleid');
        $nodeid = explode(',',input('ids')) ?? '';
        if (is_array($nodeid) && count($nodeid) > 0) {  //提交得有数据，则修改原权限配置
            Db::name('softAccessUid')->where('uid_id', $roleid)->delete();
            $node = Db::name('softNode')->order('sort asc')->select();
            foreach ($node as $_v) $node[$_v['id']] = $_v;
            foreach ($nodeid as $k => $node_id) {
                $data[$k] = $this->get_nodeinfo($node_id, $node);
                $data[$k]['uid_id'] = $roleid;
            }
            $ret = Db::name('softAccessUid')->insertAll($data);
        } else {
            $ret = Db::name('softAccessUid')->where('uid_id', $roleid)->delete();
        }
        if ($ret) {
            return msg_return('成功！');
        } else {
            return msg_return('删除失败,该用户所有权限已被清空！', 1);
        }
    }

    public function get_nodeinfo($node_id, $node)
    {
        $info['node_id'] = $node[$node_id]['id'];
        $info['pid'] = $node[$node_id]['pid'];
        $info['level'] = $node[$node_id]['level'];
        if ($info['level'] == 3) {
            $sid = '';
            $c_name = $node[$node_id]['name'];
             if ($node[$node_id]['sid'] != '' && $node[$node_id]['sid'] != 0) {
                $c_name = 'sfdp';
                $sid = '?sid=' . $node[$node_id]['sid'];
            }
            $info['data'] = $c_name . '/' . $node[$node_id]['data'] . $sid;
        } else {
            $info['data'] = $node[$node_id]['data'];
        }
        return $info;
    }
}
