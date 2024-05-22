<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use Exception;
use think\facade\Db;
use think\facade\View;

class Schedule
{
    public static  function Data($softId,$start,$end){
        $map1 = [
            ['uid', '=', $softId],
            ['start_time', 'BETWEEN', [$start,$end]],
        ];
        $map2 = [
            ['uids', 'find in set', $softId],
            ['start_time', 'BETWEEN', [$start,$end]],
        ];
        $data = Db::name('softDay')
            ->whereOr([$map1,$map2])
            ->field('id,uid,title,start_time as start,end_time as end,theme as className')
            ->select()->toArray();
        foreach($data as $k=>$v){
            if($v['uid'] != $softId){
                $data[$k]['title']='[S]'.$v['title'];//分享标识符
            }
            $color=  Db::name('soft_day_type')->where('id',$v['className'])->find();
            $data[$k]['className']='layui-bg-'.($color['color'] ?? '未关联');
            $data[$k]['type']=$color['title'] ?? '未关联';
        }
        return $data;
    }

    public static  function delData($id){
        $ret = Db::name('softDay')->delete($id);
        if($ret){
            return msg_return('删除成功！');
        }else{
            return msg_return('删除失败！',1);
        }
    }

    public static  function saveData($softId,$post){
        if(isset($post['id'])){
            $post['create_time'] = time();
            $ret = Db::name('softDay')->update($post);
        }else{
            $post['uid'] = $softId;
            $post['create_time'] = time();
            $post['update_time'] = time();
            $ret = Db::name('softDay')->insertGetId($post);
        }
        if($ret){
            return msg_return('写入成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }
    public static  function saveTag($softId,$post){
        if(isset($post['id'])){
            $ret = Db::name('soft_day_type')->update($post);
        }else{
            $post['uid'] = $softId;
            $ret = Db::name('soft_day_type')->insertGetId($post);
        }
        if($ret){
            return msg_return('写入成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }
    public static  function find($id,$table='soft_day'){
        if($table=='soft_day'){
            if($id !=''){
                $row = Db::name('softDay')->find($id);
                $row['uids_val'] = (Db::name('soft_user')->where('id','in',$row['uids'])->field('group_concat(realname) as realname')->find())['realname'] ?? '未关联';
                $row['uids_val_ids']= $row['uids'];
            }else{
                $row = [];
                $row['uids_val_ids'] = 0;
            }
            return $row;
        }
        if($table=='soft_day_type'){
            if($id !=''){
                $row = Db::name('soft_day_type')->find($id);
            }else{
                $row = [];
            }
            return $row;
        }
    }
    public static  function tag($softId){
        return Db::name('soft_day_type')->where('uid','in',[0,$softId])->select()->toArray();
    }
}
