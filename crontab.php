<?php
use Crontab\Cservice;
use think\facade\Db;

require_once "vendor/autoload.php";
require_once 'extend/Crontab/Autoloader.php';
require_once 'extend/Crontab/Crontab.php';
require_once 'extend/Crontab/Parser.php';

$Dbconf = require_once 'config/database.php';
$SysCrontab = require_once 'config/SysCrontab.php';

define('SysCrontab', $SysCrontab);

date_default_timezone_set('PRC');

Db::setConfig($Dbconf);
//判断exec 函数是否可以执行
$isExec =  in_array('exec', explode(',', ini_get('disable_functions')));
if($isExec){
    echo  'exec函数被禁止使用！';exit;
}
//判断pcntl posix拓展是否安装
$isLinux = strpos(PHP_OS, "Linux") !== false ? true : false;
if ($isLinux) {
    foreach (["pcntl", "posix"] as $ext) {
        if(!in_array($ext, get_loaded_extensions())){
            echo $ext . '扩展没有安装';exit;
        }
    }
}

(new Cservice())->run();