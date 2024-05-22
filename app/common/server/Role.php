<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use Exception;
use think\facade\Db;
use think\facade\View;

class Role
{
    /*获取全部的用户角色*/
    static  function data(){
        return Db::name('soft_role')->where('status',2)->order('id asc')->select()->toArray();
    }
}
