<?php

/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use think\facade\Db;
use Tree;
use Security;

class Node extends Base
{
    /**
     * 列表
     */
    public function index()
    {
        $Node = Db::name('softNode')->order('sort asc')->select();
        $array = array();
        // 构建生成树中所需的数据
        foreach ($Node as $r) {
            $r['status'] = $r['status'] == 1 ? '<span style="color: red; ">√</span>' : '<span style="color: blue; ">×</span>';
            $r['edit'] = $r['id'] == 1 ? '<span style="color: #cccccc; ">修改</span>' : "<a onclick='layer_show(\"菜单添加\",\"" . url('edit', ['id' => $r['id'], 'pid' => $r['id']]) . "\",\"75\",\"70\")'>修改</a>";
            $r['del'] = $r['id'] == 1 ? '<span style="color: #cccccc; ">删除</span>' : "<a onclick='del(this,\"" . $r['id'] . "\")'  href='javascript:void(0)'>删除</a>";
            $r['pid_node'] = ($r['pid']) ? ' class=" child-of-node-' . $r['pid'] . '"' : '';
			$mobile = ['不启用','启用'];
			$r['mobile'] = $mobile[$r['mobile']];
			$display = ['不显示','主菜单','子菜单'];
			$r['display'] = $display[$r['display']];
			$level = ['非节点','应用','模块','方法'];
			$r['level'] = $level[$r['level']];
            $array[] = $r;
        }
        $str = "<tr  id='node-\$id' \$pid_node>
				    <td align='center'><input type='text' class='input-text layui-input' style='width:40%;display: inline;' value='\$sort' size='2' name='sort[\$id]'></td>
				    <td align='center'>\$id</td> 
				    <td class='text-l'>\$spacer \$title (\$name)</td> 
				    <td class='text-l'>\$data</td> 
				    <td align='center'>\$level</td> 
				    <td align='center'>\$status</td> 
				    <td align='center'>\$display</td> 
					 <td align='center'>\$mobile</td> 
					<td align='center'>
						\$edit | \$del
					</td>
				  </tr>";
        $Tree = new Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $Tree->init($array);
        $html_tree = $Tree->get_tree(0, $str);
        return view('index', ['html_tree' => $html_tree,'int'=>count($this->check_controller())]);
    }

    /**
     * 自动注册
     */
    public function int(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $node_ctl = [
                'name'=>strtolower($data['name']),
                'title'=>$data['title'],
                'status'=>'1','pid'=>$data['pid'],'level'=>'2','data'=>$data['data'],'display'=>2
            ];
            $pid = Db::name('soft_node')->insertGetId($node_ctl);

            foreach($data['titles'] as $k=>$v){
                $node = [
                    'name'=>strtolower($data['name']),
                    'title'=>$v,
                    'status'=>'1','pid'=>$pid,'level'=>'3','data'=>$data['names'][$k]
                ];
                Db::name('soft_node')->insertGetId($node);
            }
            return json(['code' => 0, 'msg' => '批量注册成功！']);
        }
        $data = [];
        $dataAll =  $this->check_controller();
        foreach($dataAll as $v) {
            $data[$v] = $this->check($v,1);
        }
        $common_node = [
            'add'=>'添加','edit'=>'修改','index'=>'列表','view'=>'查看','del'=>'删除'
        ];
        return view('', ['select_categorys' => $this->cate(),'data'=>$data,'common_node'=>$common_node]);
    }
    /**
     * 检测节点信息
     */
    private function check_controller(){
        $auto = [];
        $ctrl = (new Security())->node();
        foreach($ctrl as $v){
            $has =  Db::name('soft_node')->where('name',$v)->whereOr('data','like',$v.'/%')->find();
            if(!$has){
                $auto[] = $v;
            }
        }
        return $auto;
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('softNode')->insertGetId($data);
            if ($ret) {
                return msg_return('操作成功！');
            } else {
                return msg_return('出错！', 1);
            }
        } else {
            return view('', ['select_categorys' => $this->cate()]);
        }
    }
    /**
     * 修改
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('softNode')->update($data);
            if ($ret) {
                return msg_return('编辑成功！');
            } else {
                return msg_return('编辑失败', 1);
            }
        } else {
            return view('add', ['info' => Db::name('softNode')->find($id), 'select_categorys' => $this->cate()]);
        }
    }

    /**
     *删除
     */
    public function del($id)
    {
        $count = Db::name('softNode')->where('pid',$id)->count();
        if($count>0){
            return json(['code' => 1, 'msg' => '请先删除下级菜单!']);
        }
        if (Db::name('softNode')->delete($id)) {
            return json(['code' => 0, 'msg' => '删除成功！']);
        } else {
            return json(['code' => 1, 'msg' => '删除失败!']);
        }
    }
    /**
     * 节点排序
     */
    public function sort()
    {
        $sorts = input('sort');
        if (!is_array($sorts)) $this->error('参数错误!');
        foreach ($sorts as $id => $sort) {
            Db::name('softNode')->update(['id' => $id, 'sort' => intval($sort)]);
        }
        $this->success('更新完成！');
    }
    /**
     * 批量注册
     */
    public function reg($control){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            foreach($data['title'] as $k=>$v){
                $node = [
                    'name'=>strtolower($control),
                    'title'=>$v,
                    'status'=>'1','pid'=>$data['pid'],'level'=>'3','data'=>$data['name'][$k]
                ];
                Db::name('soft_node')->insertGetId($node);
            }
            return json(['code' => 0, 'msg' => '批量注册成功！']);
        }

        $data = $this->check($control);
        $common_node = [
            'add'=>'添加','edit'=>'修改','index'=>'列表','view'=>'查看','del'=>'删除'
        ];
        return view('', ['control'=>$control,'common_node'=>$common_node,'node' => $data[0], 'pid' => $data[1]]);
    }
    /**
     * 检测信息
     */
    private  function check($control,$return=0){
        $Names = [];
        $className = "app\\gadmin\\controller\\".ucwords($control);
        if (!class_exists($className) && $return==0) {
            return g_returnJSMsg("对不起，未能检测到您输入的控制器信息!");
        }
        $hasNode = Db::name('softNode')->where('name',$control)->where('level','2')->find();
        if (!$hasNode && $return==0) {
            return g_returnJSMsg("对不起，请先注册一条控制器信息!");
        }
        $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $k=>$v){
            if($v->name =='__construct'){
                unset($methods[$k]);
            }else{
                $Names[] = $v->name;
            }
        }
        if($return==1){
            return $Names;
        }
        return [$Names,$hasNode];
    }

    /**
     * 获取分类
     */
    private function cate(){
        $Node = Db::name('softNode')->where('level','in',[0,1])->select();
        $array = array();
        foreach ($Node as $r) {
            $r['disabled'] =  '';
            $array[$r['id']] = $r;
        }
        $str = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
        $Tree = new Tree();
        $Tree->init($array);
        return $Tree->get_tree(0, $str, input('pid', '0', 'intval'));
    }
    /**
     * saas层级列表
     */
    public function saas($page = 1, $limit = 20, $draw = 1, $map = []){
        if ($this->request->isPost()) {
            if (input("name")) $map[] = ['name', 'like', '%' . input('name') . '%'];
            $List = commonListData('softSaas', $map, $page, $limit);
            $json = $List['list'];
            $status = [0 => '保存中', 1 => '退回', 2 => '审核通过'];
            $jsondata = [];
            foreach ($json as $k => $v) {
                unset($json[$k]['sort']);
                $json[$k]['status'] = $status[$v['status']] ?? 'ERR';
                $jsondata[$k] = array_values($json[$k]);
            }
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $jsondata]);
        }
        return view();
    }

    /**
     * 新增
     */
    public function saas_add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('softSaas')->insertGetId($data);
            if ($ret) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加用户失败', 1);
            }
        } else {
            return view();
        }
    }
    /**
     * 编辑
     */
    public function saas_edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['uptime']=time();
            $ret = Db::name('softSaas')->where('id',$id)->update($data);
            if ($ret) {
                return msg_return('编辑成功！');
            } else {
                return msg_return('编辑失败', 1);
            }
        } else {
            return view('saas_add', ['info' => Db::name('softSaas')->find($id)]);
        }
    }
    /**
     * 状态改变
     * */
    public function saas_status($id, $status)
    {
        $data = [
            'status' => $status,
            'uptime' => time(),
            'id' => $id
        ];
        $ret = Db::name('softSaas')->update($data);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }
}
