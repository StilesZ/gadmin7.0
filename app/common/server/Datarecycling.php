<?php

/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;

class Datarecycling
{
    /*初始化创建表*/
    static function build($is_build){
        if($is_build){
            $sql = "CREATE TABLE `g_soft_datarecycling` (
                                      `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
                                      `uid` int NOT NULL COMMENT '操作人',
                                      `add_time` datetime DEFAULT NULL COMMENT '回收时间',
                                      `table` varchar(255) DEFAULT NULL COMMENT '回收表',
                                      `table_id` int NOT NULL COMMENT '回收主键',
                                      `table_data` longtext COMMENT '回收数据json',
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='数据回收表';";
            try{
                Db::execute($sql);
                $node = ['name'=>'','title'=>'数据回收','status'=>'1','pid'=>'11','level'=>'2','display'=>'2','data'=>'sys/datar'];
                Db::name('soft_node')->insertGetId($node);
            }catch(\Exception $e){
                Db::name('soft_node')->where('title','数据回收')->where('data','sys/datar')->update(['status'=>1]);
            }
        }else{
            Db::name('soft_node')->where('title','数据回收')->where('data','sys/datar')->update(['status'=>0]);
        }

    }
    /**
     * 添加回收数据
     * @param $data
     * @return void
     */
    static function add($data=[],$uid=''){
        $data['uid'] = session('softId') ?? $uid;
        $data['add_time'] = date('Y-m-d H:i:s');
        $ret = Db::name('soft_datarecycling')->insertGetId($data);
        if($ret){
            return true;
        }else{
            return false;
        }
    }




}