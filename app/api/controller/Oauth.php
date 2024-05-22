<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\api\controller;

use think\Exception;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Db;

/**
 * API鉴权验证
 */
class Oauth
{
    public static $accessTokenPrefix = 'accessToken_';

    final function authenticate()
    {      
        return self::certification(self::getClient());
    }

    public static function getClient()
    {   
		$authorization = Request::header('Authorization');
        try {
            $authorization = explode(" ", $authorization);
            $authorizationInfo  = explode(":", base64_decode($authorization[1]));
            $userinfo = Db::name('soft_user')->field('id,username,realname,tel,mail,role,sass_id')->find($authorization[0]);
            $clientInfo['uid'] = $authorization[0];
			$clientInfo['role'] = $authorizationInfo[1];
            $clientInfo['access_token'] = $authorizationInfo[0];
            $clientInfo['sys_userinfo'] = $userinfo;
            return $clientInfo;
        } catch (Exception $e) {
            return returnMsg('Invalid authorization credentials',-1);
        }
    }

    /**
     * 获取用户信息后 验证权限
     * @return mixed
     */
    public static function certification($data = []){
        $getCacheAccessToken = Cache::get(self::$accessTokenPrefix . $data['access_token']);  //获取缓存access_token
        if(!$getCacheAccessToken){
            return returnMsg('fail',-1,"access_token不存在或为空");
        }
         if($data['uid']!=$getCacheAccessToken['client']['id']){
            return returnMsg('fail',-1,"系统错误！认证失败！");
        }
        return ['uid'=>$getCacheAccessToken['client']['id'],'role'=>$getCacheAccessToken['client']['role'],'sys_userinfo'=>$getCacheAccessToken['client']['name'],'saas_id'=>$getCacheAccessToken['client']['sass_id']];
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param string $arr 需要验证权限的数组
     * @return boolean
     */
    public static function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr)
        {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr))
        {
            return true;
        }

        // 没找到匹配
        return false;
    }
}