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

class Widget
{
    /**
     * 设计的主题强制给应用用户
     * @param $id
     * @return void
     */
    static function app($id){
        $tag = self::find($id);
        $users = Db::name('soft_user')->where('status',1)->where('role',$tag['role'])->select()->toArray();
        foreach($users as $k=>$v){
            $has = Db::name('soft_widget_user')->where('uid',$v['id'])->find();
            if($has){
                Db::name('soft_widget_user')->where('uid',$v['id'])->update(['utime'=>time(),'widget'=>$tag['ids'],'layout'=>$tag['layout']]);
            }else{
                Db::name('soft_widget_user')->insertGetId(['uid'=>$v['id'],'utime'=>time(),'widget'=>$tag['ids']]);
            }
        }
        return msg_return('一键分配成功！');
    }
    /**
     * 保存主题
     * @param $post
     */
    static  function saveTag($post){
        if(isset($post['id'])){
            $post['uptime']=time();
            $ret = Db::name('soft_widget_type')->update($post);
        }else{
            $ret = Db::name('soft_widget_type')->insertGetId($post);
        }
        if($ret){
            return msg_return('操作成功！');
        }else{
            return msg_return('更新失败！',1);
        }
    }

    /**
     * 主题查找
     * @param $id
     */
    static  function find($id){
        if($id !=''){
            $row = Db::name('soft_widget_type')->find($id);
        }else{
            $row = [];
        }
        return $row;

    }

    /**
     * 主题列表
     * @return array
     */
    static  function tag(){
        return Db::name('soft_widget_type')->order('id desc')->select()->toArray();
    }

    /**
     * 获取组件内容
     * @param $type 插件类别
     */

    static  function data($type=0,$is_app=0){
        $widget =  Db::name('soft_widget')->where('type',$type)->where('is_app',$is_app)->select()->toArray();
        $widgets = [];
        foreach ($widget as $k=>$v){
            $widgets[$v['id']] = $v;
        }
        return $widgets;
    }
    static function hasSfdp($sid){
        $find = Db::name("sfdp_widget")->where('sid', $sid)->value('widget');
        if(!$find){
            return false;
        }
        return  Desk::sfdpData($find,session('softId'), session('sfotRoleId'));
    }

}
