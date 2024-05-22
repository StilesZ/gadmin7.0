<?php

namespace event;

use think\facade\Db;

class cnt{


	/**
 * 添加后事件
 * @param   id [新增后的id]
 * @param   data [Post传递参数]
 */
 public function add_after($params = []){



	return ["code"=>0,"msg"=>"success"];
}
	

	/**
 * 修改前事件
 * @param   id [id]
 * @param   data [Post传递参数]
 */
 public function edit_before($params = []){



	return ["code"=>0,"msg"=>"success"];
}
	


}
?>