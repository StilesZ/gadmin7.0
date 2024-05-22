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
use tpflow\adaptive\Bill;
use tpflow\lib\unit;
use tpflow\service\method\Jwt as workflow;
use tpflow\Api;
use tpflow\adaptive\Process;
use tpflow\adaptive\User;

class Tpflow
{
	static function logData($table,$id){	
		 $log = (new workflow())->wfAccess('log',['id'=>$id,'type'=>$table]);
		 return json_decode($log,true);
	}
	static function wfMysend($page=1,$limit=20,$map=[]){
        $List = Api::wfMysend($page,$limit,$map);
        foreach($List['data'] as $k=>$v){
            $app_config = Db::name('soft_app_config')->where('table',$v['type'])->find();
            $List['data'][$k]['appid'] = $app_config['mid'] ?? '';
            $run = Db::name('wf_run')->find($v['run_id']);
            $List['data'][$k]['bid'] = (int)$run['from_id'];
            /*处理标题数据回去*/
            $bill_info = Bill::getbill($run['from_table'],$run['from_id']);
            $List['data'][$k]['bill_info'] =$bill_info;
            $List['data'][$k]['tmp'] =self::tmpToVal($v['tmp'],$bill_info);
            $List['data'][$k]['sid'] = Db::name('sfdp_design')->where('s_db',$v['type'])->value('id') ?? '';
            $List['data'][$k]['time_ver'] =unit::is_time($v['is_time'],$v['js_time'],$v['bl_time']);
            $List['data'][$k]['urls'] =self::changeUrl($v);
        }
        return ['data'=>$List['data'],'count'=>$List['count']];
    }

	static function getWork($map=[],$page=1,$limit=20){
       if(empty($map)){
           $map=[['f.status','=',0]];
       }
        $List = Api::wfUserData('userFlow',$map,'','f.id desc','',$page,$limit);
		$List['data'] = $List['data']->all();
        $List['data2'] = $List['data2']['data'];
		foreach($List['data'] as $k=>$v){
			$app_config = Db::name('soft_app_config')->where('table',$v['type'])->find();
			$List['data'][$k]['appid'] = $app_config['id'] ?? '';
			$run = Db::name('wf_run')->find($v['run_id']);
			$List['data'][$k]['bid'] = (int)$run['from_id'];
            /*处理标题数据回去*/
            $bill_info = Bill::getbill($run['from_table'],$run['from_id']);
            $List['data'][$k]['bill_info'] =$bill_info;

            $List['data'][$k]['tmp'] =self::tmpToVal($v['tmp'],$bill_info);
            $List['data'][$k]['sid'] = Db::name('sfdp_design')->where('s_db',$v['type'])->value('id') ?? '';
            $List['data'][$k]['time_ver'] =unit::is_time($v['is_time'],$v['js_time'],$v['bl_time']);
            $List['data'][$k]['urls'] =self::changeUrl($v);
		}
        foreach($List['data2'] as $k=>$v){
            $run = Db::name('wf_run')->find($v['run_id']);
            $List['data2'][$k]['bid'] = (int)$run['from_id'];
            /*处理标题数据回去*/
            $bill_info = Bill::getbill($run['from_table'],$run['from_id']);
            $List['data2'][$k]['bill_info'] =$bill_info;
            $List['data2'][$k]['tmp'] ='[会签]'.self::tmpToVal($v['tmp'],$bill_info);
            $List['data2'][$k]['sid'] = Db::name('sfdp_design')->where('s_db',$v['type'])->value('id') ?? '';
            $List['data2'][$k]['time_ver'] ='';
            $List['data2'][$k]['urls'] =self::changeUrl($v);
        }
        $List['data'] = array_merge($List['data'],$List['data2']);

		return $List;
	}

    static function mySing($page=1,$limit=20,$map=[]){
        $List = Api::wfMysing($page,$limit,$map);
        foreach($List['data'] as $k=>$v){
            $run = Db::name('wf_run')->find($v['run_id']);
            $List['data'][$k]['bid'] = (int)$run['from_id'];
            /*处理标题数据回去*/
            $bill_info = Bill::getbill($run['from_table'],$run['from_id']);
            $List['data'][$k]['bill_info'] =$bill_info;
            $List['data'][$k]['tmp'] ='[会签]'.self::tmpToVal($v['tmp'],$bill_info);
            $List['data'][$k]['sid'] = Db::name('sfdp_design')->where('s_db',$v['type'])->value('id') ?? '';
            $List['data'][$k]['time_ver'] ='';
            $List['data'][$k]['urls'] =self::changeUrl($v);
        }
        return ['data'=>$List['data'],'count'=>$List['count']];
    }
    static function tmpToVal($tmp,$info){
        if(!$info){
            return '';
        }
        $strSubject = $tmp;
        $strPattern = "/(?<=【)[^】]+/";
        $arrMatches = [];
        preg_match_all($strPattern, $strSubject, $arrMatches);
        foreach($arrMatches[0] as $k1 => $v1){
            if (strpos($v1, '@') !== false) {
                $v1_array = explode("@", $v1);
                $v1_value = $info[$v1_array[0]];
                $v1_rvalue = Bill::getbillvalue($v1_array[1],$v1_value,$v1_array[2]) ?? ' sys field err ';
                $strSubject = str_ireplace(['【' . $v1 . '】'], [$v1_rvalue], $strSubject);
            }else{
                $strSubject = str_ireplace(['【' . $v1 . '】'], [($info[$v1] ?? ' sys field err ')], $strSubject);
            }
        }
        return $strSubject;

    }


    static function changeUrl($v){
        if (strpos($v['wf_action'], '@') !== false) {
            $urldata = explode("@",$v['wf_action']);
            $url = url(unit::gconfig('int_url') . '/' . $urldata[0] . '/' . $urldata[1], ['id' => $v['from_id'], $urldata[2] => $urldata[3]]).($urldata[4] ?? '');
        } else if(strpos($v['wf_action'], '%') !== false){
            //增加了自定义网址
            $url = str_replace("%", "", $v['wf_action']).$v['from_id'];
        }else {
            if (strpos($v['wf_action'], '/') !== false) {
                $url = url(unit::gconfig('int_url') . '/' . $v['wf_action'], ['id' => $v['wf_fid']]);
            }else{
                $url = url(unit::gconfig('int_url') . '/' . $v['from_table'] . '/' . $v['wf_action'], ['id' => $v['from_id']]);
            }
        }
        return $url;

    }
	static function getAccess($table,$id,$info){
		//执行wf权限判断
        $wf_data = (new workflow())->wfaccess('btn',['id'=>$id,'type'=>$table,'status'=>$info['status']]);
		if(isset($wf_data['status'])){
			return -1;
		}
		$flowinfo = (new workflow())->WfCenter('Info',$id,$table);
		if($flowinfo =='' || empty($flowinfo)){
			return -1;
		}
		$npidata =  self::nexnexprocessinfo($flowinfo['status']['wf_mode'],$flowinfo['nexprocess']);
		if($npidata['code']==0){
			$flowinfo['npi'] = 1;
			$flowinfo['npicon'] = $npidata['data'];
		}else{
			$flowinfo['npi'] = 0;
			$flowinfo['npicon'] = $npidata['data'];
		}
		
		$preprocess = Process::GetPreProcessInfo($flowinfo['run_process']);
		$pre = [];
		
		foreach($preprocess as $k=>$v){
			array_push($pre,['value'=>$k,'label'=>$v]);
		}
		$flowinfo['preprocess'] = $pre;
		$flowinfo['user'] = User::GetUser();
		return $flowinfo;
	}
	static function nexnexprocessinfo($wf_mode,$npi){
		if($wf_mode!=2){
			if($npi['auto_person']!=3){
				$data = $npi['process_name'].'('.$npi['todo'].')';
				return ['data'=>$data,'code'=>0];
			}else{
				$todu = [];
				foreach($npi['todo']['ids'] as $k=>$v){
					$todu[$k]['value'] = $v.'*%*'.$npi['todo']['text'][$k];
					$todu[$k]['label']= $npi['todo']['text'][$k];
				}
				return ['data'=>$todu,'code'=>1];
			}
		}else{
			$pr = '[同步]';
			$op ='';
			foreach($npi as $k=>$v){
				   $op .=$v['process_name'].'('.$v['todo'].')'; 
			}
			return ['data'=>$pr.$op,'code'=>0];
		}
	}
	static function saveWf($post){
		$wf_fid = $post['wf_fid'];
		$wf_type = $post['wf_type'];
		$wf_op = $post['submit_to_save'];
		if($wf_op=='sign'){
			$post['submit_to_save']='sing';
		}
		if($wf_op=='sok' || $wf_op=='sback' ||$wf_op=='ssing' ){
			$wf_op = 'sign';
		}
		$submit = input('submit') ?? 'ok';
		$wf = (new workflow())->WfCenter('do',$wf_fid,$wf_type,['wf_op'=>$wf_op,'submit'=>$submit],$post);
		if($wf['code']==0){
			return 'success';
		}else{
			return $wf['msg'];
		}
	}
}