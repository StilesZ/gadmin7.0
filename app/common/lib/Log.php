<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\lib;

class Log
{
    public static function getSysLog()
    {
        $request = app('request');
        //运行时长
        $runtime = round(microtime(true) - app()->getBeginTime(), 10);
        //内存使用量
        $memory_use = number_format((memory_get_usage() -  app()->getBeginMem()) / 1024, 2);
        //用户提交的参数
        $params = json_encode(input("param."), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$logs = app()->log->getLog();
        $info = [
			'type' =>key($logs) ?? 'INFO',
            'controller'       => $request->controller(),
            'uri'       => $request->url(),
            'params'    => $params,
            'ip'    => $request->ip(),
            'runtime' => number_format($runtime, 6) . 's',
            'memory'  => $memory_use . 'kb',
			'sql' =>json_encode($logs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			'create_time'  => time()
        ];
        return $info;
    }
}