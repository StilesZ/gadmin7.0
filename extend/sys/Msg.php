<?php

namespace sys;

use app\common\server\Msg as Msgserver;
use tpflow\adaptive\Bill;
use think\facade\Db;
use app\common\server\Tpflow;

/**
 *+------------------
 * Gadmin系统
 *+------------------
 */
class Msg {

    /**
     * 工作流消息接口
     * @param $pid 运行中的步骤id
     * @return void
     */
	public function send($pid){
		//找到运行步骤
        $info = Db::name('wf_run_process')->find($pid);
        //找到运行的实例
        $run = Db::name('wf_run')->find($info['run_id']);
		//找出办理人员id
        self::addMsg(self::getuids($info,$run,0),$run['from_id'],$run['from_table'],$run['flow_id']);
	}
    /**
     *增加会签消息节点
     * @param $run_id 运行步骤id
     * @param $uid  接收人id
     * @return void
     */
    public function sing_msg($user_id,$run_id){
        $run = Db::name('wf_run')->find($run_id);
        self::addMsg($user_id,$run['from_id'],$run['from_table'],$run['flow_id']);
    }

    /**
     *增加消息节点
     * @param $run_id 运行步骤id
     * @param $pid  节点消息id
     * @return void
     */
    public function node_msg($run_id,$pid){
        //找到消息步骤
        $info = Db::name('wf_flow_process')->find($pid);
        //找到运行的实例
        $run = Db::name('wf_run')->find($run_id);
        self::addMsg(self::getuids($info,$run),$run['from_id'],$run['from_table'],$run['flow_id']);
    }

    /**
     * @param $info 步骤消息信息
     * @param $run 运行表信息
     */
    public static function getuids($info,$run,$type=1){
        if ($info['auto_person'] == 2 || $info['auto_person'] == 3 || $info['auto_person'] == 4) { //自由选择
            if($type==1){
                $user_id = $info['auto_sponsor_ids'];
            }else{
                $user_id = $info['sponsor_ids'];
            }
        }
        if ($info['auto_person'] == 5) { //办理角色
            if($type==1){
                $role_ids = $info['auto_role_ids'];
            }else{
                $role_ids = $info['sponsor_ids'];
            }
            $user =Db::name('soft_user')->where('role','in',$role_ids)->column('id');
            $user_id =implode(',',$user);
        }
        if ($info['auto_person'] == 6) { //事务接收者
            $pro = Db::name('wf_flow_process')->find($info['run_flow_process']);
            if($pro['work_ids']==1){
                $user_id = Bill::getbillvalue($run['from_table'], $run['from_id'], $pro['work_text']);
            }
            //角色
            if($pro['work_ids']==2){
                $role_ids = Bill::getbillvalue($run['from_table'], $run['from_id'], $pro['work_text']);
                $user =Db::name('soft_user')->where('role','in',$role_ids)->column('id');
                $user_id =implode(',',$user);
            }
        }
        return $user_id;
    }

    /**
     * @param $user_id 用户id,可能格式为，2
     * @param $from_id 单据id
     * @param $from_table 单据表
     */
    public static function addMsg($user_id,$from_id,$from_table,$flow_id){
        $tmp = Bill::getbillvalue('wf_flow',$flow_id,'tmp');
        $info = Bill::getbill($from_table,$from_id);
        if(!$info){
            return;
        }
        $msg = '您有一条消息:'.Tpflow::tmpToVal($tmp,$info);
        $data = Db::query("show table status where Name='g_".$from_table."'");
        $title = str_replace("[work]","",$data[0]['Comment']) ?? '业务';
        //循环遍历写入数据库
        $uid_array = explode(",",$user_id);
        $app = app('http')->getName();
        if($app =='api'){
            $oauth = app('app\api\controller\Oauth');
            $userinfo =  $oauth->authenticate();;
            $uid = $userinfo['uid'];
        }
        if($app =='gadmin'){
            $uid = session('softId');
        }
        foreach ($uid_array as $v){
            Msgserver::mAdd($v,$uid,$title, $msg,$from_id,$from_table);
        }
    }
}
?>