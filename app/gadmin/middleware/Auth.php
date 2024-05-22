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

namespace app\gadmin\middleware;

use Closure;
use Rbac;
use think\facade\Session;

class Auth
{

    public function handle($request, Closure $next)
    {
		/*新增系统防火墙*/
		$SysWaf = config('SysWaf');
		if(is_array($SysWaf) && isset($SysWaf['waf_open']) && $SysWaf['waf_open'] == 1){
			$checKstr = urldecode($_SERVER['REQUEST_URI']) . urldecode($_SERVER['HTTP_COOKIE'] ?? '') . urldecode(file_get_contents('php://input')) . implode('', getallheaders());
			foreach ($SysWaf['waf'] as $key=>$rule) {
				if (preg_match('^' . $rule . '^i', $checKstr)) {
					if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
						return json(['code'=>1,'msg'=>'对不起您的操作已被防火墙拦截！规则为：【'.$key.'】']);
					}else{
						return redirect((string)url('Login/waf'));
					}
				}
			}
		}
        /*登入唯一性校验*/
        if(!is_signin()){
            Session::clear();
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
                return json(['code'=>1,'msg'=>'对不起，您的账号已在其他设备登录,请刷新后重新登入！']);
            }else{
                return redirect((string)url('Login/noauth',['title'=>'对不起，您的账号已在别处登录,请重新登入！','btn'=>'重新登入']));
            }
        }
		/*系统防火墙结束*/
        defined('softId') or define('softId', session('softId'));
        if (null === softId) {
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
                return json(['code'=>1,'msg'=>'长时间未操作，已退出登入！']);
            }else{
                return redirect((string)url('Login/index'));
            }
        } else {
            if (!session(config('rbac.admin_auth_key'))) {
                if (!Rbac::checkAccess()) {
                   if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
                        return json(['code'=>1,'msg'=>'对不起，您没有操作权限！']);
                    }else{
                       return redirect((string)url('Login/noauth'));
                   }
                }
            }
        }
        return $next($request);
    }
}