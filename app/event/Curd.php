<?php
declare (strict_types = 1);
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\event;

/**
 * 系统事件处理
 * Class Curd
 * @package app\middleware
 */
class Curd
{
	/**
	 * @param array $params
	 * @return array
	 */
	function handle($params=[])
	{
		/**前置判断主要参数是否存在**/
		if(!isset($params['name_db']) || !isset($params['act']) ){
			return ['code'=>0,'msg'=>'参数：name_db 或 act 不存在'];//注意此处不会报错，只是将监听事件全部失效
		}
		$table = strtolower(str_replace('_','',$params['name_db']));
		$act = $params['act'];
		/**检查参数类是否存在**/
		$className = '\\event\\'.$table;
		if (class_exists($className)) {
			$directory = new $className();
			/**检测当前方法是否存在**/
			if(method_exists($directory,$act)){
				return (new $className())->$act($params);//执行系统参数类
			}
		}
	}
	
}