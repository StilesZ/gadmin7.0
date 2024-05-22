<?php
/**
 *+------------------
 * Gadmin 6.0 企业级开发平台
 *+------------------
 * Copyright (c) 2021~2025 https://gadmin8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace app\api\controller\v1;

use app\common\server\Tpflow;
use app\Request;
use app\api\controller\Api;
use think\facade\Db;
use think\facade\View;
use think\Exception;
use sfdp\Api as sfdpApi;
use app\common\server\Sfdp as SfdpServer;
use tpflow\service\method\Jwt as workflow;

class Sfdp extends Api
{
    //protected $noAuth = ['init','getData','getSysCon'];

    public function index($sid,$page=1)
    {
        $data = (new sfdpApi($sid))->sfdpCurd('index', $sid);
        $config = $data['config'];
        /*添加动作*/
        $ret_event = GyEvent('CurdEvent',['name_db'=>$data['config']['table'],'act'=>'list_before','data'=>['data'=>$data,'btn'=>[]],'id'=>$sid]);
        if($ret_event['code'] == 1){
            $config = $ret_event['msg']['config'] ?? $config;
        }
        if($config['fun'] != ''){
            $preg='/<script .*?src="(.*?)".*?>/is';
            preg_match($preg,$config['fun'],$matches);
            $config['fun'] = '//'.request()->host().':'.request()->port().$matches[1];
        }
        $whereraw = '';
        if(input('keyword') && count($data['config']['search_field'])>0){
            foreach ($data['config']['search_field'] as $v){
                $whereraw .= $v.' LIKE '.'"%'.input('keyword').'%" or ';
            }
        }
        $st ='';
        if(input('st') || input('st') ==0){
            $st = 'status = '.input('st');
        }

        if(input('keyword') && count($data['config']['search_field'])>0){
            $raw = '('.rtrim($whereraw,' or ').') and ('.$st.')';
        }else{
            $raw = $st;
        }

        $list = (new sfdpApi($sid))->sfdpCurd('GetData', $sid,'',['page'=>$page,'limit'=>15,'whereRaw'=>$raw]);
        $listData = $list['list'];
        $jsondata = [];
        $btnArray = $list['list']['field']['btn'];
        $tablename = $list['list']['field']['db_name'];
        foreach ($listData['list'] as $k => $v) {
           $wf = '';
            if (in_array('WorkFlow', $btnArray)) {
                $wf = \tpflow\Api::wfaccess('btn', ['id' => $v['id'], 'type' => $tablename, 'status' => $v['status']]);
            }
            $ret_event = GyEvent('CurdEvent',['name_db'=>$tablename,'act'=>'list_after','data'=>$v,'id'=>$sid]);
            if($ret_event['code'] == 1 && is_array($ret_event['msg'])){
                foreach($ret_event['msg'] as $kk=>$vv){
                    $listData['list'][$k][$kk] = $vv;
                }
            }
            $jsondata[$k] = $listData['list'][$k];
        }
        if(in_array('WorkFlow',$data['btn']) && in_array('Status',$data['btn'])){
            $data['btn'] =  $this->delByValue($data['btn'],'Status');
        }
        return returnMsg('success',200,['placeholder'=>'1','data'=>$jsondata,'config' => $config, 'btns' => $this->userBtnAccess($sid,$data['btn']), 'sid' => $sid,'count_field'=>$data['config']['count_field']]);
    }
    public function delByValue($arr, $value){
        foreach($arr as $k=>$v){
            if($v == $value){
                unset($arr[$k]);
            }
        }
        return $arr;
    }
    public function userBtnAccess($sid,$btn){
        if($this->uid ==1){
            return $btn;
        }
        $role = Db::name('softUser')->where('id',$this->uid)->value('role');
        $data = Db::name('soft_access')->where('role_id',$role)->column('node_id');
        $data2 = Db::name('soft_access_uid')->where('uid_id',$this->uid)->column('node_id');
        $data3 = array_keys(array_flip($data)+array_flip($data2));
        $userAccess = Db::name('soft_node')->where('sid',$sid)->where('id','in',$data3)->column('data');
        foreach($btn as $k=>$v){
            if(!in_array(strtolower($v),$userAccess)){
                unset($btn[$k]);
            }
        }
        return $btn;
    }
    /*获取初始化数据*/
    public function sinit($sid,$id='')
    {
        if($id==''){
            $wf =-1;
            $showtype ='add';
            $data = (new sfdpApi($sid))->sfdpCurd('add', $sid);
        }else{
            $wf =[];
            $showtype ='edit';
            $data = (new sfdpApi($sid))->sfdpCurd('edit', $sid, $id);
            $bill_data =json_decode($data['data'],true);
            if (in_array('WorkFlow', $bill_data['tpfd_btn'])){
                try {
                    $bill_field = Db::name($bill_data['name_db'])->find($id);
                }catch(Exception $e) {
                    return returnMsg('系统错误~',-1);
                }
                $wf = [
                    'log'=>Tpflow::logData($bill_data['name_db'],$id),
                    'wf'=>Tpflow::getAccess($bill_data['name_db'],$id,$bill_field),
                    'wf_fid'=>$id,
                    'wf_type'=>$bill_data['name_db']
                ];
            }else{
                $wf = -1;
            }
        }
        $config = $data['config'];
        if($config['fun'] != ''){
            $preg='/<script .*?src="(.*?)".*?>/is';
            preg_match($preg,$config['fun'],$matches);
            $config['fun'] = '//'.request()->host().':'.request()->port().$matches[1];
        }
        return returnMsg('success',200,['wf'=>$wf,'showtype' => $showtype, 'config' => $config, 'data' => $data['data']]);
    }
    /*添加数据*/
    public function add($sid){
        if ($this->request->isPost()) {
            $info= (new sfdpApi($sid))->sfdpCurd('info', $sid);
            $info = json_decode($info['data'],true);
            $s_sys_check = $info['s_sys_check'] ?? '0';
            return SfdpServer::Sfdp_save($this->request->post(),$this->uid,$s_sys_check,1);
        }
    }
    /*修改数据*/
    public function edit($id){
        if ($this->request->isPost()) {
            return SfdpServer::Sfdp_edit($this->request->post(),$id,200,$this->uid,1);
        }
    }
    /*删除数据*/
    public function del($id, $sid,$table)
    {
        /*添加前执行的动作*/
        $ret_event = GyEvent('CurdEvent',['act'=>'del_fun','id'=>$id,'name_db'=>$table]);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        $view = (new sfdpApi($sid))->sfdpCurd('del', $sid, $id);
        if ($view) {
            SfdpServer::oplog($table,$id,2,'',$this->uid);
            return msg_return('删除成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }
    /*WF流程接口*/
    public function send($table,$id){
        $wf_id = Db::name('wf_flow')->where('type',$table)->where('status',0)->value('id');
        if(!$wf_id){
            return msg_return('Success,未能找到流程引擎！！',1);
        }
        $wf = (new workflow())->WfCenter('start','','',['wf_fid'=>$id,'wf_id'=>$wf_id,'check_con'=>'app-发起']);
        if(!isset($wf)){
            return msg_return('Success,未能找到流程引擎！！',1);
        }
        if($wf['code']=='-1'){
            return msg_return($wf['msg'],1);
        }else{
            return msg_return('Success,送审成功！');
        }
    }

    public function status($id, $table, $status)
    {
        $ret = SfdpServer::status($id, $table, $status,$this->uid);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }
    public function sapi($name){
        $Source = app('app\gadmin\controller\Source');
        $data = json_decode(($Source->api($name,'',0,['uid'=>$this->uid,'role'=>$this->role]))->getContent(),true);
        return returnMsg('获取成功！',200,$data['data']);
    }



}