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

namespace app\api\controller;

use think\Request;
use think\facade\Cache;

/**
 * 生成token
 */
class Token
{
	/**
	 * 请求时间差
	 */
	public static $timeDif = 10000;

	public static $accessTokenPrefix = 'accessToken_';
	public static $expires = 7200*30*10;

	/**
	 * appsercet
	 */
	public static $appsercet = '4Fz0r0r1pf3yGm1MdD5UGxiJ0pHDP';

	/**
	 * 生成token
	 */
	public function token($request=[],$userInfo=[])
	{
		if(abs($request['timestamp'] - time()) > self::$timeDif){
			return self::returnMsg('请求时间戳与服务器时间戳异常',-1,'timestamp：'.time());
		}
		//签名检测
		$sign = makeSign($request,self::$appsercet);
		if($sign !== $request['sign']){
			return self::returnMsg('sign错误',-1,'sign：'.$sign);
		}
		try {
			$accessToken = self::setAccessToken(array_merge($userInfo,$request));  //传入参数应该是根据手机号查询改用户的数据
			return $accessToken;
		} catch (Exception $e) {
			return self::returnMsg('fail',-1,$e);
		}
	}

	/**
     * 设置AccessToken
     * @param $clientInfo
     * @return int
     */
    protected function setAccessToken($clientInfo)
    {
        //生成令牌
        $accessToken = self::buildAccessToken();
        $accessTokenInfo = [
            'access_token'  => $accessToken,//访问令牌
            'expires_time'  => time() + self::$expires,      //过期时间时间戳
            'client'        => $clientInfo,//用户信息
        ];
        self::saveAccessToken($accessToken, $accessTokenInfo);  //保存本次token
        return $accessTokenInfo;
    }

    /**
     * 生成AccessToken
     * @return string
     */
    protected static function buildAccessToken($lenght = 32)
    {
        //生成AccessToken
        $str_pol = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz";
		return substr(str_shuffle($str_pol), 0, $lenght);

    }

    /**
     * 存储token
     * @param $accessToken
     * @param $accessTokenInfo
     */
    protected static function saveAccessToken($accessToken, $accessTokenInfo)
    {
        //存储accessToken
        cache(self::$accessTokenPrefix . $accessToken, $accessTokenInfo, self::$expires);
    }

}