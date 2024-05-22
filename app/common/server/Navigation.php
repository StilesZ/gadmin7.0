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
namespace app\common\server;

use think\facade\Db;

class Navigation
{
    public static function node($id){
        $yxids = Db::name('soft_navigation')->where('pid',$id)->column('nid');
        $node = [];
        $data =  Db::name('softNode')->where('id','not in',$yxids)->where('level',2)->where('pid','<>',10)->where('data','<>','')->select()->toArray();
        foreach ($data as $k=>$v){
            if(strpos($v['data'],'sys') === false && strpos($v['data'],'Slog') === false && strpos($v['data'],'report') === false){
                $node[$k]['id'] = $v['id'];
                $node[$k]['title'] = $v['title'];
                $node[$k]['url'] = $v['data'];
            }
        }
        return $node;
    }
    public static function navigation_data($id){
        $process_data = [];
        $data = Db::name('soft_navigation')->where('pid',$id)->select()->toArray();
        foreach($data as $k=>$v){
            $url = Db::name('soft_node')->where('id',$v['nid'])->value('data') ?? '';
            $process_data[] = [
                'id' => $v['id'],
                'mode' => '',
                'name' => $v['title'],
                'flow_id' => $v['pid'],
                'process_name' => '编号:'.$v['id'],
                'process_to' => $v['process_to'],
                'url' =>'/gadmin/'.str_replace("index","add",$url),
                'style' => 'width:140px;height:auto;line-height:30px;border-radius: 4px;color:#2d6dcc;left:' . $v['left'] . 'px;top:' . $v['top'] . 'px;',
            ];
        }
        return json_encode(['list'=>$process_data]);
    }
    public static function saveDesic($post,$act='save'){
        if($act=='save'){
            $process_info = $post['process_info'];
            $process_info = json_decode(htmlspecialchars_decode(trim($process_info)), true);
            foreach ($process_info as $process_id => $value) {
                $datas = [
                    'top' => (int)$value['top'],
                    'left' => (int)$value['left'],
                    'process_to' => implode(',',$value['process_to']),
                    'update_time' => time()
                ];
                $ret = Db::name('soft_navigation')->where('id',$process_id)->update($datas);
            }
        }
        if($act=='del'){
            $ret = Db::name('soft_navigation')->delete($post['id']);
        }
        if($act=='node'){

            $search = Db::name('softNode')->where('data','sys/navigation_show?id='.$post['sid'])->find();
            if($search){
                return msg_return('对不起，已被挂载！！',1);
            }

            $info = Db::name('soft_navigation_type')->find($post['sid']);
            $node_top = ['status'=>1,'data'=>'sys/navigation_show?id='.$post['sid'],'name'=>'sys','title'=>$info['title'],'pid'=>$post['node'],'level'=>2,'display'=>2,'sid'=>''];
            $ret =  Db::name('softNode')->insertGetId($node_top);
        }
        if($ret){
            return msg_return('写入成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }
    public static  function saveData($softId,$post){
        foreach($post['ids'] as $v){
            $data['uid'] = $softId;
            $data['title'] = Db::name('softNode')->where('id',$v)->value('title');
            $data['nid'] = $v;
            $data['pid'] = $post['pid'];
            $data['update_time'] = time();
            $ret = Db::name('soft_navigation')->insertGetId($data);
        }
        if($ret){
            return msg_return('写入成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }
    public static  function saveTag($softId,$post){
        if(isset($post['id'])){
            $ret = Db::name('soft_navigation_type')->update($post);
        }else{
            $post['uid'] = $softId;
            $ret = Db::name('soft_navigation_type')->insertGetId($post);
        }
        if($ret){
            return msg_return('写入成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }
    public static  function find($id,$table='soft_day'){
        if($table=='soft_navigation_type'){
            if($id !=''){
                $row = Db::name('soft_navigation_type')->find($id);
            }else{
                $row = [];
            }
            return $row;
        }
    }
    public static  function tag($softId){
        return Db::name('soft_navigation_type')->where('uid','in',[0,$softId])->select();
    }


}
