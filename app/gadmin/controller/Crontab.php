<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use think\facade\Db;
use think\facade\Request;


class Crontab extends Base
{
    /**
     * 列表方法
     */
    public function index($map = [])
    {
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("name")) $map[] = ['title', 'like', '%'.input('name').'%'];
            $list = Db::name('softCrontab')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            foreach ($list as $k => $v) {
                $list[$k]['last_time'] = date('Y-m-d H:i:s', $v['last_time']);
            }
            $count = Db::name('softCrontab')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['create_time'] = time();
            $userId = Db::name('softCrontab')->insertGetId($data);
            if (isset($userId)) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加失败', 1);
            }
        } else {
            return view('add');
        }
    }
    /**
     *用户编辑
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['update_time'] = time();
            $res = Db::name('softCrontab')->where('id',$id)->update($data);
            if (isset($res)) {
                return msg_return('编辑成功！');
            } else {
                return msg_return('编辑用户失败', 1);
            }
        } else {
            return view('add', ['info' => Db::name('softCrontab')->find($id)]);
        }
    }
    public function logs($id,$map = [])
    {
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
            $list = Db::connect('db_log')->name('crontab_log')->where('pid',$id)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            foreach ($list as $k => $v) {
                $list[$k]['status'] = $v['status'] == 0 ? '<span class="layui-badge-rim layui-bg-blue">OK</span>' : '<span class="layui-badge-rim layui-bg-black">ERR</span>';
                $list[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            }
            $count = Db::connect('db_log')->name('crontab_log')->where('pid',$id)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view();
    }
    public function run($act){
        $url = config('SysCrontab.crontab_name');
        echo $this->work_curl($url.$act);
    }
    public function change($id,$status)
    {
        if (Db::name('softCrontab')->update(['id'=>$id,'status'=>$status])) {
            return json(['msg' => '操作成功！', 'code' => 0]);
        } else {
            return json(['msg' => '操作成功！', 'code' => 1]);
        }
    }
    public function work_curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        curl_close($ch);
        if($result==false){
            return json_encode(['code'=>-1,'msg'=>'Link Err']);
        }
        return $result;
    }
}