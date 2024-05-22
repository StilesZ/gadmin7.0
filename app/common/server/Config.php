<?php

namespace app\common\server;

use think\facade\Db;

class Config
{
    /**
     * 初始化将配置文件写入数据库
     */
    public static function Build(){
        if(count(g_cache('g_soft_cache_config'))<=0){
            $sys = config('gadmin');
            foreach ($sys as $k=>$v){
                //系统默认的配置项
                $oldconfigname = ['app_version'=>'app_version','sys_page'=>'sys_page','name'=>'公司名称','sysname'=>'系统名称','sysv'=>'系统版本',
                    'copy'=>'版权信息','icp'=>'备案号','logimg'=>'登录页背景','logo'=>'登入页LOGO','home_logo'=>'后台首页LOGO',
                    'view'=>'模板配置','office'=>'LibreOffice','verify'=>'启用验证码','viewmode'=>'阅读模式','voffice'=>'voffice', 'username'=>'授权appid',
                    'password'=>'授权密码', 'g_api'=>'官网接口', 'is_dd'=>'启用钉钉',
                    'wf_qs'=>'去审核权','is_wx'=>'启用微信','iptoken'=>'IP位置接口','sfdp_db'=>'删表权限','sfdp_fix'=>'部署权限','desktype'=>'桌面版本', 'is_api'=>'Api接口开关', 'print'=>'打印版本',
                    'watermark'=>'页面水印','datarecycling'=>'数据回收','online'=>'在线用户','is_login'=>'单一登入',
                ];
                Db::name('soft_config')->insertGetId(['value'=>$v,'only_tag'=>$k,'name'=>$oldconfigname[$k] ?? '','update_time'=>time(),'type'=>0]);
            }
            Config::Init(1);
        }
    }
    /**
     * 系统配置信息初始化
     */
    public static function Init($status = 0)
    {
        $key = 'g_soft_cache_config';
        $data = g_cache($key);
        if($data === null || $status == 1){
            $data = Db::name('soft_config')->column('value', 'only_tag');
            if(!empty($data)){
                foreach($data as $k=>&$v){
                    g_cache($k, $v);// 公共内置数据缓存
                }
            } else {
                $data = [];
            }
            // 所有配置缓存集合
            g_cache($key, $data);
        }
        $sp_dictionary = g_cache('sp_dictionary');

        if($sp_dictionary === null || $status == 1) {
            /*缓存系统数据字典-详情信息*/
            $info = Db::name('soft_dictionary_d')
                ->alias('b')
                ->join('soft_dictionary a', 'a.id = b.dict_id')
                ->where('a.status', '=', 2)
                ->where('b.status', '=', 2)
                ->field('b.id,b.detail_name,a.dict_code,b.bg_color,b.ft_color,b.detail_value,b.content')
                ->select()->toArray();
            $common_key = [];
            $fields_key = [];
            foreach ($info as $v) {
                $fields_key[$v['dict_code']][] = $v;
                $common_key[$v['dict_code'] . '_' . $v['detail_value']] = $v;
            }
            g_cache('sp_dictionary', $common_key);
            g_cache('sp_dictionary_key', $fields_key);
        }
    }
}