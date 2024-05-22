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

use app\common\server\Tpflow;
use think\facade\Db;
use think\response\Json;
use ThinkApi;
use app\common\server\Msg;
use Dingtalk;
use Weixin;

class Api extends Base
{
	public function tApi($act)
	{
		$data = input('');
		if($data['act']=='lWeather' || $data['act']=='oTxt' || $data['act']=='eCode' || $data['act']=='eInfo' || $data['act']=='qrcode'){
			$ret = ThinkApi::$act($data['keyword']);
		}
		if($data['act']=='sms'){
			if($data['params']=='code'){
				$random = rand(111111,999999);//生成随机验证码
				$params ='{"code": '.$random.'}';
			}else{
				$params = $data['params'];
			}
			$ret = ThinkApi::sms($data['phone'],$params,$data['templateId']);
			if($data['params']=='code'){
				cache('iphone_'.$data['phone'],$random, 300);//缓存短信有效期5分钟
			}
		}
		if($data['act']=='oCard'){
			$ret = ThinkApi::oCard($data['image'],$data['side']);
		}
		return json($ret);
	}
	
	public function c_code(){
		$phone = input('iphone');
		$code = input('code');
		if($phone =='' || $code ==''){
			return json(['code'=>1,'msg'=>'手机号码/验证码不能为空！']);
		}
		//判断验证码是否正确
		if(cache('iphone_'.$phone)===$code){
			return json(['code'=>0,'msg'=>'验证码验证成功！']);
		}else{
			return json(['code'=>1,'msg'=>'tip:验证码出错！']);
		}
	}
	
    /**
     * 列表方法
     */
    public function getMsg() : Json
    {
        $ret = Db::connect('db_log')->name('log')->whereTime('create_time','-30 Minutes')->find();
        if(!$ret){
            return json(['code'=>-1]);
        }
    	$ret = $this->sendMsg();
    	if($ret['code']==1){
            return json($ret);
        }
		$Total = Msg::MessageTotal(['uid'=>session('softId'),'is_read'=>0]);
        $map[]=['f.status','=',0];
        $map[]=['r.status','=',0];
        $mydata = Tpflow::getWork($map,1,1000);
        return json(['code'=>0,'Total'=>$Total,'flow'=>count($mydata['data'])]);
    }
    public function sendMsg(){
        $wf_type = config('msg.wf_type');//工作流发送模式-1，关闭 0，邮件，1短信
        $o_type = config('msg.o_type');//其他消息发送模式  -1，关闭 0，邮件，1短信
        //找出所有未发送的单据信息
		$msg = Db::name('soft_message')->where('is_send',0)->select();
		foreach($msg as $v){
            if($v['yw_type']=='' || $v['yw_id']=='' || $v['yw_id']==0){
                $check_bill = false;
            }else{
                $check_bill = Db::name($v['yw_type'])->where('status','<>',2)->where('id',$v['yw_id'])->find();
            }
		    if(!$check_bill){
                $this->uplog($v['id']);
            }else{
                //工作流通知模式
                if($v['type']==0 && $wf_type >= 0){
                    $userInfo = Msg::getUser($v['uid'],$wf_type);
                    if(!$userInfo && $userInfo == ''){
                        return ['code'=>1,'msg'=>'未配置用户电话或者邮箱'];
                    }
                    if($wf_type == 0){
                        //发送邮件
                        $ret = ThinkApi::mail($userInfo,'编号：['.$v['yw_id'].']'.$v['title'], $v['content']);
                    }
                    if($wf_type == 1){
                        //发送短信
                        $ret = ThinkApi::sms($userInfo,'{"content": "编号：['.$v['yw_id'].']'.$v['title'].'"}',config('msg.wfmid'));
                    }
					if($wf_type == 2){
						//钉钉消息推送
						$ret = (new Dingtalk())->sendMsg($userInfo,'您需要审核：编号：['.$v['yw_id'].']的'.$v['title'].'工作流业务！');
					}
                    if($wf_type == 3){
                        //钉钉消息推送
                        $ret = (new Weixin())->sendMsg($userInfo,'您需要审核：编号：['.$v['yw_id'].']的'.$v['title'].'工作流业务！');
                    }
                    //发送成功，更新状态
                    if($ret['code'] == 0){
                        $this->uplog($v['id']);
                    }else{
                        return ['code'=>1,'msg'=>'发送消息失败，请通知管理员查看！'];
                    }
                }else{
                    $this->uplog($v['id']);
                }
                //其他消息通知模式
                if($v['type']==1 && $o_type >= 0){
                    $userInfo = Msg::getUser($v['uid'],$wf_type);
                    if(!$userInfo && $userInfo == ''){
                        return ['code'=>1,'msg'=>'未配置用户电话或者邮箱'];
                    }
                    if($wf_type == 0){
                        //发送邮件
                        $ret = ThinkApi::mail($userInfo,'编号：['.$v['yw_id'].']'.$v['title'], $v['content']);
                    }
                    if($wf_type == 1){
                        //发送短信
                        $ret = ThinkApi::sms($userInfo,'{"content": "编号：['.$v['yw_id'].']'.$v['title'].'"}',config('msg.wfmid'));
                    }
					if($wf_type == 2){
						//钉钉消息推送
						$ret = (new Dingtalk())->sendMsg($userInfo,'您需要审核：编号：['.$v['yw_id'].']的'.$v['title'].'工作流业务！');
					}
                    if($wf_type == 3){
                        //钉钉消息推送
                        $ret = (new Weixin())->sendMsg($userInfo,'您需要审核：编号：['.$v['yw_id'].']的'.$v['title'].'工作流业务！');
                    }
                    //发送成功，更新状态
                    if($ret['code'] == 0){
                        $this->uplog($v['id']);
                    }else{
                        return ['code'=>1,'msg'=>'发送消息失败，请通知管理员查看！'];
                    }
                }else{
                    $this->uplog($v['id']);
                }
            }
        }
        return ['code'=>0];
	}
    private function uplog($uid){
        Msg::MessageSend(['id'=>$uid]); //重置消息为已发送
    }
}
