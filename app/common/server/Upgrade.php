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

class Upgrade
{
	static function glogin(){
		$data = ['username'=>g_cache('username'),'password'=>g_cache('password'),'ip'=>GetHostByName($_SERVER['SERVER_NAME']),'mac'=> ''];
		return self::Curl(g_cache('g_api').'/v1/login',true,'post',$data);
	}
	static function sys_info(){
		$g_info = self::get_file(root_path() . 'ver.json');
        if ($g_info === false) {
           return false;
        } 
		return $g_info;
	}
	/**
     * 遍历当前目录不包含下级目录
     */
    static function scan_dir($dir,$file='')
    {
        if (trim($dir) == '') {
            return false;
        }
		
        $file_arr = scandir($dir);
        $new_arr = [];
        foreach($file_arr as $item){

            if($item!=".." && $item !="." && $item != $file){
                $new_arr[] = $item;
            }
        }
        return $new_arr;

    }
	/**
     * 解压缩
     */
    static function open_zip($file,$todir)
    {
        if (trim($file) == '') {
            return -1;
        }
        if (trim($todir) == '') {
            return -1;
        }
        $zip = new \ZipArchive;
        if ($zip->open($file) === TRUE) {
            $zip->extractTo($todir);
            $zip->close();
            unlink($file);
            $result = 200;
        } else {
            $result = -1;
        }
        return $result;
    }
    /**
	* url     Url网址
	*/
    static function Curl($url,$https=true,$method='get',$data=null,$headers=null){
        if (trim($url) == '') {
            return false;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($https === true) {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        }
		
        if ($method == 'post') {
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
		if ($headers != null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
        $str = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);
        if(intval($aStatus["http_code"])==200){
            return json_decode($str);
        }else{
            return false;
        }
    }
    /**
     * 获取文件内容
     */
    static function del_files(){
        $del = self::get_file(root_path() . 'public/update/cache/del.json');
        if($del){
            if(isset($del->file) && $del->file!=''){
                $del_file_list = explode('|',$del->file);
                foreach ($del_file_list as $v){
                    try{
                        unlink($v);
                    }catch(\Exception $e){
                        self::write_file(date('Y-m-d').'    '.$e->getMessage().PHP_EOL);
                    }
                }
            }
            if(isset($del->dir) && $del->dir!=''){
                $del_dir_list = explode('|',$del->dir);
                foreach ($del_dir_list as $v){
                    try{
                        self::deldir( root_path().$v);
                    }catch(\Exception $e){
                        self::write_file(date('Y-m-d').'    '.$e->getMessage().PHP_EOL);
                    }
                }
            }
        }
    }
	 /**
     * 获取文件内容
     */
    static function get_file($url){
        if (trim($url) == '') {
            return false;
        }
        $op = ['http'=>['method'=>"GET",'timeout'=>3]];
        $co=0;
        while($co<3 && ($res=@file_get_contents($url, false, stream_context_create($op)))===FALSE) $co++;
        if ($res === false) {
            return false;
        } else {
            return json_decode($res);
        }
    }
	 /**
     * 获取sql文件内容
     */
   static function get_sql($url){
        if (trim($url) == '') {
            return false;
        }
        $op = ['http'=>['method'=>"GET",'timeout'=>3]];
        $co=0;
        while($co<3 && ($res=@file_get_contents($url, false, stream_context_create($op)))===FALSE) $co++;
        if ($res === false) {
            return false;
        } else {
            return $res;
        }
    }
	/**
     * 合并目录且只覆盖不一致的文件
     */
    static function copy_merge($source, $target) {
        if (trim($source) == '') {
            return false;
        }
        if (trim($target) == '') {
            return false;
        }
        $source = preg_replace ( '#/\\\\#', DIRECTORY_SEPARATOR, $source );
        $target = preg_replace ( '#\/#', DIRECTORY_SEPARATOR, $target );
        $source = rtrim ( $source, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        $target = rtrim ( $target, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        $count = 0;
        if (! is_dir ( $target )) {
            mkdir ( $target, 0777, true );
            $count ++;
        }
        foreach ( glob ( $source . '*' ) as $filename ) {
            if (is_dir ( $filename )) {
                $count += self::copy_merge ( $filename, $target . basename ( $filename ) );
            } elseif (is_file ( $filename )) {
                if (! file_exists ( $target . basename ( $filename ) ) || md5 ( file_get_contents ( $filename ) ) != md5 ( file_get_contents ( $target . basename ( $filename ) ) )) {
                    copy ( $filename, $target . basename ( $filename ) );
                    $count ++;
                }
            }
        }
        return $count;
    }
	/**
     * 遍历删除文件
     */
    static function deldir($dir) {
        if (trim($dir) == '') {
            return false;
        }
        $dh=opendir($dir);
            while ($file=readdir($dh)) {
                if($file!="." && $file!="..") {
                  $fullpath=$dir."/".$file;
                  if(!is_dir($fullpath)) {
                      unlink($fullpath);
                  } else {
                     self::deldir($fullpath);
                  }
            }
        }
        closedir($dh);
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 遍历执行sql文件
     */
    static function up_sql($dir){
        if (trim($dir) == '') {
            return false;
        }
        $sql_file_res = self::scan_dir($dir);
        if (empty($sql_file_res)) {
            return true;
        }else{
            foreach ($sql_file_res as $k => $v) {
                if (!empty(strstr($v,'.sql'))) {
                    $sql_content = self::get_sql($dir.$v);
                    $sql_arr = explode(';', $sql_content);
                    foreach ($sql_arr as $vv) {
                        $sql_info = trim($vv);
                        if (!empty($sql_info)) {
                            /*加强升级校验*/
                            $sql = trim($sql_info.';');
                            try{
                                Db::execute($sql);
                            }catch(\Exception $e){
                                self::write_file(date('Y-m-d').$e->getMessage().PHP_EOL);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    /**
     * 下载程序压缩包文件
     */
   static function down_file($url, $save_dir,$filename,$context) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            return false;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
		$streamData = file_get_contents($url, false, $context);
		if($streamData){
			file_put_contents($save_dir.$filename, $streamData, true);
			return ['status' =>0 ,'file_name' => $filename,'save_path' => $save_dir . $filename];
		}else{
			return false;
		}
    }
    static function write_file($l2='')
    {
        return @file_put_contents(root_path().'error.log', $l2, FILE_APPEND);
    }

}