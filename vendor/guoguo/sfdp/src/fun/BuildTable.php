<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 构建数据库表
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */

namespace sfdp\fun;

use think\Exception;
use sfdp\lib\unit;
use sfdp\adaptive\Common;


class BuildTable{
	/**
     * 创建数据表
     */
    static function Btable($table,$data,$btn,$name,$all='')
    {
		if (in_array($table, unit::gconfig('black_table'))) {
			return ['msg'=>'该数据表不允许创建','code'=>1];
        }
        $tableName = unit::gconfig('int_db_prefix') . $table;
        $tableExist = false;
		// 判断表是否存在,如果存在，就创建个备份数据表
        $ret = Common::query("SHOW TABLES LIKE '{$tableName}'");
        if ($ret && isset($ret['msg'][0])) {
            Common::execute("RENAME TABLE {$tableName} to {$tableName}_bak");
            $tableExist = true;
        }
		//内置字段
        //tpfd_del  tpfd_saas
        $auto_create_field = ['id','uid','status', 'create_time', 'update_time'];
        if(isset($all['tpfd_del']) && $all['tpfd_del']==0){
            array_push($auto_create_field,"is_delete");
        }
        if(isset($all['tpfd_saas']) && $all['tpfd_saas']==0){
            array_push($auto_create_field,"saas_id");
        }
        if(isset($all['s_sys_check']) && $all['s_sys_check']==1){
            array_push($auto_create_field,"create_ip");
            array_push($auto_create_field,"create_os");
        }
        $fieldAttr = [];
		$fieldAttr[] = unit::tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'";
        $key = [];
        foreach ($data as $field) {
            if (!in_array($field['tpfd_db'], $auto_create_field)) {
				if($field['tpfd_dblx']=='datetime'||$field['tpfd_dblx']=='longtext' ||$field['tpfd_dblx']=='date'){
					$fieldAttr[] = unit::tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']} COMMENT '{$field['tpfd_name']}'";
					}else{
					$fieldAttr[] = unit::tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']}({$field['tpfd_dbcd']}) DEFAULT NULL COMMENT '{$field['tpfd_name']}'";
				}
            }
        }
		$fieldAttr[] = unit::tab(1) . "`uid` int(10) DEFAULT '0' COMMENT '用户id'";
		$fieldAttr[] = unit::tab(1) . "`status` int(10)  DEFAULT '0' COMMENT '审核状态[-1:退回修改 0:正常 1:流程中 2:审批完成]'";
		$fieldAttr[] = unit::tab(1) . "`create_time` int(10)  DEFAULT '0' COMMENT '新增时间'";
		$fieldAttr[] = unit::tab(1) . "`update_time` int(10)  DEFAULT '0' COMMENT '更新时间'";
		if((in_array('WorkFlow',$btn)) || (in_array('Status',$btn))){
			$fieldAttr[] = unit::tab(1) . "`uptime` int(10)  DEFAULT '0' COMMENT '工作流调用更新时间'";
		}
        if(in_array('saas_id',$auto_create_field)){
            $fieldAttr[] = unit::tab(1) . "`saas_id` varchar(255) DEFAULT NULL COMMENT '关联租户id'";
        }
        if(in_array('create_ip',$auto_create_field)){
            $fieldAttr[] = unit::tab(1) . "`create_ip` varchar(50) DEFAULT NULL COMMENT '创建ip地址'";
            $fieldAttr[] = unit::tab(1) . "`create_os` varchar(100) DEFAULT NULL COMMENT '创建客户端信息'";
        }
        if(in_array('is_delete',$auto_create_field)){
            $fieldAttr[] = unit::tab(1) . "`is_delete` int(11) DEFAULT '0' COMMENT '关联软删除字段[0:正常 1:删除]'";
        }
		$fieldAttr[] = unit::tab(1) . "PRIMARY KEY (`id`)";
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";//删除数据表
		if((in_array('WorkFlow',$btn))){
			$sql_create = "CREATE TABLE `{$tableName}` (\n"
				. implode(",\n", array_merge($fieldAttr, $key))
				. "\n)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '[work]{$name}'";
        }else{
			$sql_create = "CREATE TABLE `{$tableName}` (\n"
				. implode(",\n", array_merge($fieldAttr, $key))
				. "\n)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '{$name}'";
		}
        $ret = Common::execute($sql_drop);
		if($ret['code']==-1){
			return ['msg'=>'<h2>系统级错误：'.$ret['msg'].'</h2>','code'=>-1];
		}
        $ret2 = Common::execute($sql_create);
       if($ret2['code']==-1){
			return ['msg'=>'<h2>系统级错误：'.$ret2['msg'].'</h2>','code'=>-1];
		}
		return ['msg'=>'创建成功！','code'=>0];
    }
	static function hasDbbak($table){
        $tableName = unit::gconfig('int_db_prefix') . $table;
		$ret_bak = Common::query("SHOW TABLES LIKE '{$tableName}_bak'");
		if ($ret_bak && isset($ret_bak['msg'][0])) { 
			return ['code'=>1,'msg'=>'备份数据表已经存在，请先删除！'];
		}else{
			return ['code'=>0,'msg'=>'未找到备份数据表！'];
		}
	}
	static function delDbbak($table){
        $tableName = unit::gconfig('int_db_prefix') . $table;
		$ret_bak = Common::query("SHOW TABLES LIKE '{$tableName}_bak'");
		if ($ret_bak && isset($ret_bak['msg'][0])) { 
			try {
				$ret = Common::execute("DROP TABLE IF EXISTS `{$tableName}_bak`");
			} catch (\Exception $e) {
				return ['code'=>1,'msg'=>'系统异常。'.$e->getMessage()];
			}
			return ['code'=>0,'msg'=>'备份表已经删除！'];
		}else{
			return ['code'=>1,'msg'=>'备份表不存在！'];
		}
	}
    static function buildIndex($table,$field,$index){
        $tableName = unit::gconfig('int_db_prefix') . $table;
        $sql = 'ALTER TABLE '.$tableName.' ADD '.$index.' ('.$field.')';
        try {
             Common::execute($sql);
        } catch (\Exception $e) {
            return ['code'=>1,'msg'=>'系统异常。'.$e->getMessage()];
        }
        return ['code'=>0,'msg'=>'索引创建成功！'];
    }
    static function delIndex($table,$field){
        $tableName = unit::gconfig('int_db_prefix') . $table;
        $sql = 'DROP INDEX '.$field.' ON '.$tableName;
        try {
            Common::execute($sql);
        } catch (\Exception $e) {
            return ['code'=>1,'msg'=>'系统异常。'.$e->getMessage()];
        }
        return ['code'=>0,'msg'=>'索引创建成功！'];
    }
}