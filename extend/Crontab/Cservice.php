<?php


namespace Crontab;

use Workerman\Crontab\Crontab;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Worker;
use think\facade\Db;

/**
 * 定时任务
 */
class Cservice
{
    private $worker;
    /**
     * 初始化
     */
    public function __construct( array $contextOption = [])
    {
        $this->worker = new Worker(SysCrontab['crontab_name'], $contextOption);
        $this->worker->name = SysCrontab['crontab_socketName'];
        $this->worker->count = SysCrontab['crontab_count'];
        if (isset($contextOption['ssl'])) {
            $this->worker->transport = 'ssl';
        }
        $this->worker->onWorkerStart = [$this, 'onWorkerStart'];
        $this->worker->onMessage = [$this, 'onMessage'];
    }
    public function run()
    {
        $this->outputLog(SysCrontab['crontab_socketName']." 已启动");
        Worker::runAll();
    }
    /**
     * 设置Worker 启动后 读取 soft_crontab
     */
    public function onWorkerStart($worker)
    {
        $List = Db::name('soft_crontab')->select()->toArray();
        if (!empty($List)) {
            foreach ($List as $v) {
               new Crontab($v['rule'], function () use ($v) {
                    $this->run_work($v['id'],trim($v['shell']),$v['type']);
                });
            }
        }
        return true;
    }
    /**
     * 运行
     */
    public function run_work($id,$shell,$type=0){
        $time = time();
        $this->outputLog('任务#' . $id . ' ' . $shell);
        $startTime = microtime(true);
        $res = $this->work_center($type,$shell);
        Db::name('soft_crontab')->where('id',$id)->update(['last_time'=>$time,'run_time'=>Db::raw('run_time+1')]);
        if(SysCrontab['crontab_is_runlog']==1){
            Db::connect('db_log')->name('crontab_log')->insert(['pid' => $id,'shell' => $shell, 'content' => join(PHP_EOL, (array)$res[0]), 'status' => $res[1], 'run_time' => round($res[2] - $startTime, 6), 'create_time' => $time, 'update_time' => $time]);
        }
    }
    public function work_center($type,$op){
        if ($type == 0) {
            return $this->work_exec($op);
        }else{
            return $this->work_curl($op);
        }
    }
    /**
     * 运行exec
     * @param $shell
     * @return array
     */
    public function work_exec($shell){
        exec($shell, $output, $code);
        return [$output,$code,microtime(true)];
    }
    /**
     *
     * @param $url
     * @return array
     */
    public function work_curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        curl_close($ch);
        return [$result,0,microtime(true)];
    }
    /**
     * 消息监听
     */
    public function onMessage($connection, $request)
    {
        if ($request instanceof Request) {
            $this->request = $request;
            switch (ltrim($request->path(), '/')) {
                case 'status':
                    $response ='status success';
                    break;
            }
            $connection->send(new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8',
            ], json_encode(['code' => 200, 'data' => isset($response) ? $response : false, 'msg' => 'success'.$request->path()])));
        }
    }
    /**
     * 输出日志
     */
    private function outputLog($log, $status = true)
    {
        if(SysCrontab['crontab_is_runlog']==1) {
            echo '[' . date('Y-m-d H:i:s') . '] ' . $log . ($status ? " [True] " : " [False] ") . PHP_EOL;
        }
    }
}
