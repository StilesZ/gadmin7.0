<?php

namespace sys;

use app\api\controller\Oauth;
/**
 *+------------------
 * Gadmin 开源后台系统
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
class SfdpGateway {
	
	public function GetUserInfo(){
		$app = app('http')->getName();
		if($app =='api'){
			$oauth = app('app\api\controller\Oauth'); 
			$userinfo =  $oauth->authenticate();;
			return ['uid'=>$userinfo['uid'],'role'=>$userinfo['role'],'username'=>$userinfo['uid'],'saas_id'=>$userinfo['saas_id']];
		}
		if($app =='gadmin'){
			return ['uid'=>session('softId'),'role'=>session('sfotRoleId'),'username'=>session('sfotUserName'),'saas_id'=>session('softSaasId')];
		}
	}
}
?>