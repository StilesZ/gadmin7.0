<?php
namespace app\common\middleware;
use think\Response;

/**
 * 全局跨域请求处理
 * Class CrossDomain
 * @package app\middleware
 */

class CrossDomain
{
    /**
     * 处理跨域请求
     * @param \think\Request $request
     * @param \Closure       $next
     * return void
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $origin = $request->header('Origin', '');
        //OPTIONS请求返回204请求
        if ($request->method(true) === 'OPTIONS') {
            $response->code(204);
        }
            $response->header([
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Methods'     => 'GET,POST,PUT',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers'     => '*',
            ]);
        return $response;
    }
}