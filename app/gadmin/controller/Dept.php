<?php
/**
 *+------------------
 * Gadmin 7.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use think\facade\Db;
use app\common\server\User;

class Dept extends Base
{
    /*列表数据*/
    public function index($page = 1, $limit = 20, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
            if (input("name")) $map[] = ['name', 'like', '%' . input('name') . '%'];
            $List = commonListData('softDept', $map, $page, $limit);
            $json = $List['list'];
            $status = [
                0 => '<span class="layui-badge-dot"></span> 保存',
                1 => '<span class="layui-badge-dot layui-bg-green" ></span> 流程',
                2 => '<span class="layui-badge-dot layui-bg-blue" ></span> 通过'
            ];
            $jsondata = [];
            foreach ($json as $k => $v) {
                unset($json[$k]['sort']);
                $json[$k]['pid'] = Db::name('softDept')->where('id', $v['dept_pid'])->value('dept_name');
                $json[$k]['status'] = $status[$v['status']] ?? 'ERR';
                $jsondata[$k] = array_values($json[$k]);
            }
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $jsondata]);
        }
        return view();
    }

    /*新增*/
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('softDept')->insertGetId($data);
            if ($ret) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加用户失败', 1);
            }
        } else {
            return view('add', ['role' =>User::deptTree()]);
        }
    }

    /*编辑*/
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('softDept')->where('id',$id)->update($data);
            if ($ret) {
                return msg_return('编辑成功！');
            } else {
                return msg_return('编辑失败', 1);
            }
        } else {
            return view('add', ['info' => Db::name('softDept')->find($id), 'role' => User::deptTree()]);
        }
    }

    /*状态改变*/
    public function status($id, $status)
    {
        $data = [
            'status' => $status,
            'update_time' => time(),
            'id' => $id
        ];
        $ret = Db::name('softDept')->update($data);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }

    /*结构组织树*/
    public function show()
    {
        $list = Db::name('softDept')->where('id', '>', 1)->where('status',2)->field('id,dept_name as name,dept_pid as pid')->select()->toArray();
        return view('show', ['json' => json_encode(list_to_tree($list, 'id', 'pid', 'childrens'))]);
    }
}
