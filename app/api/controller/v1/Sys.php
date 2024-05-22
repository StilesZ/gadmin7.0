<?php
/**
 *+------------------
 * Gadmin 6.0 企业级开发平台
 *+------------------
 * Copyright (c) 2021~2025 https://gadmin8.cn All rights reserved.
 *+------------------
 */
namespace app\api\controller\v1;

use app\Request;
use app\api\controller\Api;
use think\facade\Db;
use tpflow\service\method\Jwt as workflow;
use think\facade\View;
use think\Exception;
class Sys extends Api
{   
    //protected $noAuth = ['index','getApp','getData','getSysCon'];

	
    public function getData(Request $request)
    {
		$data = $request->post();
		$appid = $data['appid'];
		$page = $data['pageNum'] ?? 1;
		$keyword = $data['keyword'] ?? '';
		$sys_app = Db::name('softNode')->find($appid);
		if($sys_app['data']==''){
			return returnMsg('查不到此功能',-1);
		}
		$app = explode("/",$sys_app['data']);
		$datas = self::getAppData($app[0],$app[1],$page,$keyword);
      return returnMsg($sys_app['title']);
    }

    /***
     * 获取系统查看页面
     * @param $appid 关联 soft_app_config mid
     * @param $id  对应业务的id信息
     */
	public function getSysCon($appid,$id){
		 $app_config = Db::name('soft_app_config')->where('mid',$appid)->find();
		 if(!$app_config){
			 return returnMsg('页面信息不存在~',-1);
		 }
		try {
			 if($app_config['table']=='err' || $app_config['table']=='log' || $app_config['table']=='login'){
				 $data = Db::connect('db_log')->name($app_config['table'])->find($id);		
			 }elseif($app_config['table']=='wf'){
				 return returnMsg('暂时不支持~',-1);
			 }else{
				 $data = Db::name($app_config['table'])->find($id); 
			 }
		}catch(Exception $e) {
			return returnMsg('系统错误~',-1);
		}
		 $returnHtml = [
			'con'=>View::display($app_config['yw_content'], ['info'=>$data]),
			'topTitle'=>$app_config['title']
		 ];
		 return returnMsg('调用查看接口完成',200,$returnHtml);
	}
    /*App列表信息转换*/
	static function getAppData($app,$fun,$page,$keyword){
		switch($app)
		{
		case 'user':
			$map[] = ['username','like','%'.$keyword.'%'];
			 $list = self::listData('soft_user',$map,$page);
			 foreach($list as $k=>$v){
                 unset($list[$k]['password']);
				 $list[$k]['create_time'] =date('Y-m-d',$v['add_time']);
				 $list[$k]['title'] =$v['username'];
				 $list[$k]['content'] ='电话：'.$v['tel'].'邮箱：'.$v['mail'];
			 }
			 return ['data'=>$list,'placeholder'=>'输入用户名查找登入信息~'];
		break;
		case "wf":
		$list = (new workflow())->WfFlowCenter('wfjk');
        //var_dump($list);
		//$data = json_decode($list,true);
		foreach($list['List'] as $k=>$v){
			 $data['List'][$k]['title'] =$v['user'];
			 $data['List'][$k]['content'] ='流程任务：'.$v['flow_name'].'|接受时间：'.date("Y-m-d H:i",$v['dateline']);
		}
		return ['data'=>$data['List'],'placeholder'=>'-1']; 
		break;
		case "Slog":
		  if($fun=='err'){
			 $map[] = ['sql','like','%'.$keyword.'%'];
			 $map[] = ['sql','like','%'.$keyword.'%'];
			 $list = self::listLogData('err',$map,$page);
			 foreach($list as $k=>$v){
				 $list[$k]['title'] = $v['uri'];
				 $list[$k]['create_time'] =date('Y-m-d',$v['create_time']);
				 $list[$k]['content'] = $v['sql'];
			 }
			 return ['data'=>$list,'placeholder'=>'请输入关键词']; 
		  }
		  if($fun=='info'){
			  $map[] = ['sql','like','%'.$keyword.'%'];
			  $list = self::listLogData('log',$map,$page);
			 foreach($list as $k=>$v){
				 $list[$k]['title'] = $v['uri'];
				 $list[$k]['create_time'] =date('Y-m-d',$v['create_time']);
				 $list[$k]['content'] = $v['sql'];
			 }
			 return ['data'=>$list,'placeholder'=>'请输入关键词']; 
			  
		  }
		  if($fun=='login'){
			 $map[] = ['username','like','%'.$keyword.'%'];
			 $list = self::listLogData('login',$map,$page);
			 foreach($list as $k=>$v){
				 $list[$k]['create_time'] =date('Y-m-d',$v['login_time']);
				 $list[$k]['title'] =$v['username'];
				 $list[$k]['content'] =$v['login_location'].'-'.$v['login_browser'].'-'.$v['login_os'];
			 }
			 return ['data'=>$list,'placeholder'=>'输入用户名查找登入信息~']; 
		  }
		  break;  
		default:
		  return returnMsg('查不到此功能',-1);
		}
        return returnMsg('查不到此功能',-1);
	}
	static function listData($table,$map=[],$page=1,$field=''){
			$offset = ($page-1)*10;  
			return Db::name($table)->where($map)->limit($offset,10)->order('id desc')->field($field)->select()->toArray();
	}
	static function listLogData($table,$map=[],$page=1,$field=''){
			$offset = ($page-1)*10;  
			return Db::connect('db_log')->name($table)->where($map)->limit($offset,10)->order('id desc')->field($field)->select()->toArray();
	}
	
}
