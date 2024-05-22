<?php
/**
 *+------------------
 * Gadmin 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;

class App
{
    public static function appData(){



        $list = Db::name('soft_app_type')->select()->toArray();
        foreach($list as $k=>$v){
            $list[$k]['color'] = 'uicon-'.$v['color'];
            $apps = Db::name('soft_app_config')->where('typeid',$v['id'])->where('status',2)->select()->toArray();
            foreach ($apps as $k2=>$v2){
                $apps[$k2]['icon'] = 'uicon-'.$v2['icon'];
            }
            if(empty($apps)){
                unset($list[$k]);
            }else{
                $list[$k]['apps'] =$apps;
            }
        }
        return $list;
    }
    public static  function saveTag($softId,$post){
        if(isset($post['id'])){
            $ret = Db::name('soft_app_type')->update($post);
        }else{
            $post['uid'] = $softId;
            $ret = Db::name('soft_app_type')->insertGetId($post);
        }
        if($ret){
            return msg_return('写入成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }
    public static  function tag(){
        return Db::name('soft_app_type')->select();
    }
    public static  function find($id,$table='soft_app'){
        if($table=='soft_app_type'){
            if($id !=''){
                $row = Db::name('soft_app_type')->find($id);
            }else{
                $row = [];
            }
            return $row;
        }
    }

}
