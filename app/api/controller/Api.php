<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\api\controller;

use app\common\lib\Log as LogUtil;
use think\facade\Db;
use think\Request;

class Api
{	
    protected $request;

    protected $clientInfo;

    protected $noAuth = [];

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->init();
		$this->uid = $this->clientInfo['uid'] ?? '';
		$this->role = $this->clientInfo['role'] ?? '';
        $this->sys_userinfo = $this->clientInfo['sys_userinfo'] ?? '';
	}

	public function init()
	{
        $is_open = g_cache('is_api') ?? 2;
        if($is_open==2){
            return returnMsg('对不起API接口已关闭',-1);
        }
		if(!Oauth::match($this->noAuth)){
			$oauth = app('app\api\controller\Oauth'); 
    		return $this->clientInfo = $oauth->authenticate();
		}
        /*增加日志记录*/
        $args = LogUtil::getSysLog();
        if ($args['type'] == 'error') {
            Db::connect('db_log')->name('err')->insert($args);
        } else {
            Db::connect('db_log')->name('log')->insert($args);
        }
	}

}