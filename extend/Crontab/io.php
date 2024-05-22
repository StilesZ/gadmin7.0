<?php


namespace Crontab;

use Workerman\Worker;
use Workerman\Timer;
use PHPSocketIO\SocketIO;
use Workerman\Protocols\Http\Request;
use Workerman\Connection\TcpConnection;
use think\facade\Session;

/**
 * 定时任务
 */
class Io
{
    // 全局数组保存uid在线数据
    private $uidData = array();
    // 记录最后一次广播的在线用户数
    public function run()
    {
        global $sender_io;
        $sender_io = new SocketIO(2120);
        // 客户端发起连接事件时，设置连接socket的各种事件回调
        $sender_io->on('connection', function($socket){
            // 当客户端发来登录事件时触发
            $socket->on('login', function ($uid)use($socket){
                // 已经登录过了
                if(isset($socket->uid)){
                    return;
                }
                // 更新对应uid的在线数据
                $uid = (string)$uid;
                if(!isset($this->uidData[$uid]))
                {
                    $this->uidData[$uid] = 0;
                }
                // 这个uid有++$uidConnectionMap[$uid]个socket连接
                ++$this->uidData[$uid];
                // 将这个连接加入到uid分组，方便针对uid推送数据
                $socket->join($uid);
                $socket->uid = $uid;
                // 更新这个socket对应页面的在线数据
                $socket->emit('update_online_count', "当前<b>{$uid}</b>人在线");
            });
            // 当客户端断开连接是触发（一般是关闭网页或者跳转刷新导致）
            $socket->on('disconnect', function () use($socket) {
                if(!isset($socket->uid))
                {
                    return;
                }
                // 将uid的在线socket数减一
                global $sender_io;
                if(--$this->uidData[$socket->uid] <= 0)
                {
                    unset($this->uidData[$socket->uid]);
                }
            });
        });
        $sender_io->on('workerStart', function(){
            // 监听一个http端口
            $inner_http_worker = new Worker('http://0.0.0.0:2121');
            // 当http客户端发来数据时触发
            $inner_http_worker->onMessage = function(TcpConnection $http_connection, Request $request){
                $post = $request->post();
                $post = $post ? $post : $request->get();
                // 推送数据的url格式 type=publish&to=uid&content=xxxx
                switch(@$post['type']){
                    case 'endid':
                        global $sender_io;
                        $sender_io->to($post['id'])->emit('x_online','强制下线');
                        return $http_connection->send('ok');
                    case 'alluid':
                        return $http_connection->send(json_encode($this->uidData,true));//获取全部在线用户的ids
                }
                return $http_connection->send('fail');
            };
            // 执行监听
            $inner_http_worker->listen();
            // 一个定时器，定时向所有uid推送当前uid在线数及在线页面数
            Timer::add(1, function(){
                global $sender_io;
                $online_count_now = count($this->uidData);
                // 只有在客户端在线数变
                $sender_io->emit('online_count', "  当前<b>{$online_count_now}</b>人在线");

            });
        });
        Worker::runAll();
    }

}

