<?php
declare (strict_types=1);

/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use think\App;
use think\facade\Request;
use think\facade\View;
use \app\gadmin\middleware\Auth;
use app\common\server\Config;

/**
 * 控制器基础类
 */
abstract class Base
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [Auth::class];


    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        View::config(['view_path' => root_path() . 'view/gadmin/' . g_cache('view') . '/']);//动态控制
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        Config::Init();
        // pjax
        if (Request::has('_pjax')) {
            View::assign(['pjax' => true]);
        } else {
            View::assign(['pjax' => false]);
        }

    }

    use \think\Jump;
}