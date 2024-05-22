<?php

namespace event;

use think\facade\Db;

class cntgl{


	/**
 * 前置权限
 */
 public function before_access($params = []){
	$bef = [1123];


	return ["code"=>1,"msg"=>$bef];
}
	

	/**
 * 添加前事件
 * @param   $data [Post传递参数]
 */
 public function add_before($params = []){



	return ["code"=>0,"msg"=>"success"];
}
	


}
?>