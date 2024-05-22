<?php

namespace eventsys;

use think\facade\Db;

class sysuser{



public function user_add_after($params = []){

	var_dump($params);
}


/**
 * 添加前事件
 * @param   $data [Post传递参数]
 */
public function login_before($params = []){
  //echo '<h2>站点关闭，正在维护！</h2>';
   // exit;
	return ["code"=>0,"msg"=>"success"];
}
/**
 * 添加前事件
 * @param   $data [Post传递参数]
 */
public function login_after($params = []){
	return ["code"=>0,"msg"=>"success"];
}







}
?>