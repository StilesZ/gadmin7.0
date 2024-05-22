<?php
/**
 *+------------------
 * Gadmin 7.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Config;
use think\facade\Db;

class Dictionary extends Base
{
    /**
     * 列表方法
     */
    public function index($map = [])
    {
        if ($this->request->isPost()) {
            if (input("dict_name")) $map[] = ['dict_name', 'like', '%' . input('dict_name') . '%'];
            if (input("dict_code")) $map[] = ['dict_code', 'like', '%' . input('dict_code') . '%'];
            $offset = (input('page') - 1) * input('limit');
            $list = Db::name('soft_dictionary')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $status = [1 => '系统', 2 => '用户'];
            foreach ($list as $k => $v) {
                $list[$k]['dict_type'] = $status[$v['dict_type']];
                $list[$k]['add_time'] = date('Y-m-d',$v['add_time']);
                $list[$k]['stv'] = g_common_status($v['status']);
            }
            $count = Db::name('soft_dictionary')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view();
    }
    /**
     * 状态核准
     */
    public function status($id, $status)
    {
        $data = [
            'status' => $status,
            'update_time' => time(),
            'id' => $id
        ];
        $ret = Db::name('soft_dictionary')->update($data);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }

    /**
     * 添加头部
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['add_name'] = session('sfotUserName');
            $data['add_time'] = time();
            $data['status'] = 2;
            $data['uid'] = session('softId');
            $ret = Db::name('soft_dictionary')->insertGetId($data);
            if ($ret) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加失败', 1);
            }
        }
        return view();
    }

    /**
     * 修改头部
     *
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('soft_dictionary')->where('id',$id)->update($data);
            if ($ret) {
                return msg_return('修改成功！');
            } else {
                return msg_return('修改失败', 1);
            }
        }
        $info = Db::name('soft_dictionary')->find($id);
        return view('add', ['info' => $info]);
    }

    public function app_tag($id='',$did=''){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['dict_id'] = $id;
            $data['update_time'] = time();
            $data['status'] = 2;
            $data['uid'] = session('softId');
            if($did!=''){
                $ret = Db::name('soft_dictionary_d')->where('id',$did)->update($data);
            }else{
                $ret = Db::name('soft_dictionary_d')->insertGetId($data);
            }
            if ($ret) {
                return msg_return('操作成功！');
            } else {
                return msg_return('操作失败', 1);
            }
        }
        $color = Db::name('soft_dictionary_d')->where('dict_id',1)->select()->toArray();
        return view('tag',['c'=>$color,'info'=>Db::name('soft_dictionary_d')->find($did),'type'=>Db::name('soft_dictionary_d')->where('dict_id',$id)->select()->toArray(),'id'=>$id]);
    }

    public function config(){
        Config::Init(1);
        return msg_return('操作成功！');
    }
}
