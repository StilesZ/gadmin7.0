<?php

namespace sys;

use think\facade\Db;
/**
 *+------------------
 * Gadmin 开源后台系统
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
class UserFun {
	
	public function value($type,$value){
		if($type =='system_user'){
			$find = Db::name('soft_user')->find($value);
			if(!$find){
				return '';
			}
            return $find['username'];
		}
        if($type =='system_role'){
            $find = Db::name('soft_role')->find($value);
            if(!$find){
            	return '';
			}
            return $find['name'];
        }
	}
}
?>