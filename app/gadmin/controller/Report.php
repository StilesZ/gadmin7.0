<?php
/**
 *+------------------
 * Gadmin 3.0 企业级开发平台
 *+------------------
 * Copyright (c) 2021~2025 https://gadmin8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Upload;
use Exception;
use think\facade\Db;
use Fkreport\adaptive\Data;
use think\facade\Filesystem;

class Report extends Base
{
    /**
     * 报表查看
     */
    public function views_s()
    {
        $map = input('get.');
        $id = Db::name('soft_report')->where('title', $map['action'])->value('id');
        $key = array_keys($map);
        return $this->views($id, [$key[1]=>$map[$key[1]]]);
    }

    /**
     * 报表查看
     */
    public function views($id, $map = [])
    {
        $info = Db::name('soft_report')->find($id);
        if ($info['status'] != 2) {
            echo '<h2>Sorry,该报表未被核准！</h2>';
            exit;
        }
        $rtype = $info['type'];
		$Source = app('app\gadmin\controller\Source');
        $rSql = $info['sql'];
        $headInfo = Db::name('soft_report_head')->find($info['head']);
        $field = json_decode($headInfo['field'], true);

        if ($this->request->isPost()) {
        	$atts = json_decode($headInfo['field'], true);
            $map = input('post.');
			foreach ($atts as $k=>$v){
				if($v['type']=='like'){
					$map[$v['name']] ='%'.$map[$v['name']].'%';
				}
			}
        }
		$ret = json_decode(($Source->apiId( $info['sid'],$map))->getContent(),true);
        /*构建查询表头*/
        $html = $this->buildHead($field, $map);
		$result = $ret['data'] ?? [];
        if (count($result) >= 1) {
            $dataHead = array_keys($result[0]);
        } else {
            g_returnJSMsg('报表查询结果为空');
        }
        /*构建SQL语句*/
        if ($rtype == 1) {
			$count = [];
			foreach($dataHead as $k=>$v){
				if(strpos($v,'*count') !== false){
					$value = explode('*',$v);
					$last_names = array_column($result,$v);
					$count[$value[0]] = ['sum'=>array_sum($last_names) ,'count'=>count($last_names)];
				}
			}
            return view('views', ['result' => $result, 'head' => $dataHead, 'headInfo' => $html['html'],'count'=>$count]);
        }else {
            /*柱形图*/
            $color = ['#429842', '#1890ff', '#df5667', '#888888', '#25c6c8', '#c87825', '#333', '#ea2dec', '#5a98de'];
            foreach ($dataHead as $v) {
                $data[] = array_values(array_column($result, $v));
            }
            $js = '';
            foreach ($data as $k2 => $v2) {
                if ($k2 >= 1) {
                    $js .= "{
						  name: '" . ($dataHead[$k2] ?? '') . "',
						  type: 'line',
						  symbolSize:10,
						  symbol:'circle',
						  itemStyle: {
							color: '" . ($color[$k2] ?? '#1890ff') . "',
							borderColor: '#fff',
							borderWidth: 2,
						  },
						  data: " . json_encode($data[$k2]) . ",
						},";
                }
            }
            $datax = json_encode($data[0]);
            $datay = json_encode($data[1]);
            return view('views_c', ['info' => $info, 'result' => json_encode($result), 'datax' => $datax, 'js' => $js, 'datay' => $datay, 'headInfo' => $html['html']]);
        }
    }

    /**
     * 头部构建
     */
    public function buildHead($data, $map = [])
    {
        $html = '';
        $where = '';
        foreach ($data as $v) {
            if ($v['type'] == "select") {
                $f_html = '';
                if (strpos($v['fun'], '@') !== false) {
                    $urldata = explode("@", $v['fun']);
                    $Source = app('app\gadmin\controller\Source');
                    $data =  $Source->api($urldata[1]);
                    $function = json_decode($data->getContent(),true);
                    if($function['code'] == 1){
                        echo '<h2>系统级别错误('.$v['fun'].')</h2>';exit;
                    }
                    $function = $function['data'];

                } else {
                    $function = json_decode($v['fun'], true);
                }
                foreach ($function as $v2) {
                    if(empty($map)){
                        $f_html .= '<option value="' . $v2['id'] . '" >' . $v2['name'] . '</option>';
                    }else{
                        if($map[$v['name']]==$v2['id']){
                            $f_html .= '<option selected value="' . $v2['id'] . '" >' . $v2['name'] . '</option>';
                        }else{
                            $f_html .= '<option value="' . $v2['id'] . '" >' . $v2['name'] . '</option>';
                        }
                    }
                }
                if (isset($map[$v['name']]) && $map[$v['name']] != '') {
                    $where .= $v['name'] . '="' . $map[$v['name']] . '" and ';
                }
                $html .= '<div class="layui-input-inline"><select class="select layui-input" name=' . $v['name'] . '><option value="" >请选择'.$v['title'].'</option>' . $f_html . '</select></div>';
            }
            if ($v['type'] == "between") {
                if ((isset($map[$v['name'] . '1']) && $map[$v['name'] . '1'] != '') && (isset($map[$v['name'] . '2']) && $map[$v['name'] . '2'] != '')) {
                    $where .= $v['name'] . ' between "' . $map[$v['name'] . '1'] . '" and "' . $map[$v['name'] . '2'] . '" and';
                }
                $html .= '<div class="layui-input-inline"><input class="datetime layui-input" value="'.input($v['name']. '1').'" id="ddd1" placeholder="开始时间" style=width:auto  name="' . $v['name'] . '1"></div><div class="layui-input-inline"><input class="datetime input-text layui-input" value="'.input($v['name']. '2').'" id="ddd2" style=width:auto  name="' . $v['name'] . '2" placeholder="结束时间"></div>';
            }
            if ($v['type'] == "like") {
                $value = '';
                if (isset($map[$v['name']]) && $map[$v['name']] != '') {
                    $where .= $v['name'] . ' like "%' . $map[$v['name']] . '%" and ';
                    $value = input($v['name']);
                }
                $html .= '<div class="layui-input-inline"><input class="input-text layui-input" placeholder="'.$v['title'].'" value="'.$value.'" style=width:auto name=' . $v['name'] . '></div>';
            }
        }
        return ['html' => $html, 'where' => $where];
    }

    /**
     * 列表方法
     */
    public function index($page = 1, $limit = 10, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
            if (input("title")) $map[] = ['title', 'like', '%' . input('title') . '%'];
            $List = commonListData('soft_report', $map, $page, $limit,'id,title,head,type,sid,status,add_name,add_time');
            $json = $List['list'];
            $jsondata = [];
            $stv = [1 => 'Data-Excel', 2 => 'ECharts-Chart'];
            $status = [0 => '保存中', 1 => '退回', 2 => '审核通过'];
            foreach ($json as $k => $v) {
                $json[$k]['head'] = get_common_val('soft_report_head',$v['head']);;
				$json[$k]['sid'] = get_common_val('soft_source',$v['sid']);
                $json[$k]['type'] = $stv[$v['type']] ?? 'ERR';
                $json[$k]['status'] = $status[$v['status']] ?? 'ERR';
                $jsondata[$k] = array_values($json[$k]);
            }
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $jsondata]);
        }
        $node = (new \sys\Node())->GetNode();
        return view('index', ['node' => $node]);
    }

    /**
     * 添加报表
     */
    public function add_report()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['add_name'] = session('sfotUserName');
            $data['add_time'] = date('Y-m-d H:i:s');
            $ret = Db::name('soft_report')->insertGetId($data);
            if ($ret) {
                return msg_return('发布成功！');
            } else {
                return msg_return($ret['data'], 1);
            }
        }
        $table = Db::name('soft_report_head')->order('id desc')->select();
		$source = Db::name('soft_source')->where('stype',2)->order('id desc')->select();
        return view('add_report', ['table' => $table,'source' => $source]);
    }

    /**
     * 编辑报表
     */
    public function edit_report($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('soft_report')->update($data);
            if ($ret) {
                return msg_return('修改成功！');
            } else {
                return msg_return('修改失败', 1);
            }
        }
        $info = Db::name('soft_report')->find($id);
        $table = Db::name('soft_report_head')->order('id desc')->select();
		$source = Db::name('soft_source')->where('stype',2)->order('id desc')->select();
        return view('add_report', ['table' => $table, 'info' => $info,'source' => $source]);
    }

    /**
     * 报表状态
     */
    public function status_report($id, $status)
    {
        $data = [
            'status' => $status,
            'update_time' => time(),
            'id' => $id
        ];
        $ret = Db::name('soft_report')->update($data);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }

    /**
     * 删除报表
     */
    public function del_report($id)
    {
        $ret = Db::name('soft_report')->delete($id);
        if ($ret) {
            return msg_return('删除成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }

    /**
     * 头部设计
     */
    public function base($page = 1, $limit = 10, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
            $status = [0 => '保存中', 1 => '退回', 2 => '审核通过'];
            if (input("title")) $map[] = ['title', 'like', '%' . input('title') . '%'];
            $List = commonListData('soft_report_head', $map, $page, $limit);
            $json = $List['list'];
            $jsondata = [];
            $stv = [1 => 'Data-Excel', 2 => 'ECharts-Chart'];
            $status = [0 => '保存中', 1 => '退回', 2 => '审核通过'];
            foreach ($json as $k => $v) {

                $json[$k]['status'] = $status[$v['status']] ?? 'ERR';
                $jsondata[$k] = array_values($json[$k]);
            }

            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $jsondata]);
        }
        return view('base');
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
        $ret = Db::name('soft_report_head')->update($data);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }

    /**
     * 删除头部
     */
    public function del($id)
    {
        $ret = Db::name('soft_report_head')->delete($id);
        if ($ret) {
            return msg_return('删除成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }

    /**
     * 添加头部
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $field = [];
            foreach ($data['val'] as $k => $v) {
                $field[$k] = [
                    'name' => $v,
                    'title' => $data['titles'][$k],
                    'type' => $data['type'][$k],
                    'fun' => $data['select'][$v] ?? ''
                ];
            }
            unset($data['val']);
            unset($data['type']);
            unset($data['select']);
            unset($data['titles']);
            $data['field'] = json_encode($field, true);
            $data['add_name'] = session('sfotUserName');
            $data['add_time'] = date('Y-m-d H:i:s');
            $ret = Db::name('soft_report_head')->insertGetId($data);
            if ($ret) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加失败', 1);
            }
        }
        $table = Db::name('sfdp_design')->order('ID desc')->select();
        return view('add', ['table' => $table]);
    }

    /**
     * 修改头部
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $field = [];
            foreach ($data['val'] as $k => $v) {
                $field[$k] = [
                    'name' => $v,
                    'title' => $data['titles'][$k],
                    'type' => $data['type'][$k],
                    'fun' => $data['select'][$v] ?? ''
                ];
            }
            unset($data['val']);
            unset($data['type']);
            unset($data['select']);
            unset($data['titles']);
            $data['field'] = json_encode($field, true);
            $ret = Db::name('soft_report_head')->update($data);
            if ($ret) {
                return msg_return('修改成功！');
            } else {
                return msg_return('修改失败', 1);
            }
        }
        $info = Db::name('soft_report_head')->find($id);
        $field = json_decode($info['field'], true);
        $table = Db::name('sfdp_design')->order('ID desc')->select();
        return view('add', ['table' => $table, 'info' => $info, 'field' => $field]);
    }

    /**
     * 头部设置
     */
    public function head($id)
    {
        $info = Db::name('soft_report_head')->find($id);
        $field = json_decode($info['field'], true);
        $html = $this->buildHead($field);
        return json(['code' => 0, 'data' => $html['html']]);
    }

    /**
     * 挂在目录
     */
    public function menu($id, $node)
    {
        $top_id = Db::name('softNode')->where('data', 'report/views?id=' . $id)->find();
        if ($top_id) {
            return json(['code' => 1, 'msg' => '对不起该栏目已经生成！']);
        }
        $report = Db::name('soft_report')->find($id);
        $node_top = ['status' => 1, 'data' => 'report/views?id=' . $id, 'name' => 'report', 'title' => $report['title'], 'pid' => $node, 'level' => 2, 'display' => 2, 'sid' => ''];
        $top_id = Db::name('softNode')->insertGetId($node_top);

        if (!$top_id) {
            return json(['code' => 1, 'msg' => 'err']);
        }
        return json(['code' => 0, 'msg' => 'success']);
    }

    /**
     * 执行SQL
     */
    public function ajax_sql($sql)
    {
        if ($this->request->isPost()) {
            try {
                $data = Db::execute($sql);
                return json(['code' => 0, 'msg' => $data]);
            } catch (Exception $e) {
                return json(['code' => -1, 'msg' => 'SQL_Err:' . $sql]);
            }
        }
        return json(['code' => -1, 'msg' => 'SQL_Err:' . $sql]);
    }

    /**
     * 列表方法
     */
    public function fk($page = 1, $limit = 10, $draw = 1, $map = [])
    {
        $node = (new \sys\Node())->GetNode();
        return view('fk', ['node' => $node,'data'=>Data::all()]);
    }

    public function upload(){
        $info = (new Upload())->up(0);
        if($info){
            Db::name('fk_imags')->insertGetId($info);
        }
        return json(['code' => 0, 'data' => $info['src']]);
    }
    /**
     * 挂在目录
     */
    public function menu2($id, $node)
    {
        $top_id = Db::name('softNode')->where('data', 'fk/api?act=view&id=' . $id)->find();
        if ($top_id) {
            return json(['code' => 1, 'msg' => '对不起该栏目已经生成！']);
        }
        $report = Db::name('fk_main')->find($id);
        $node_top = ['status' => 1, 'data' => 'fk/api?act=view&id=' . $id, 'name' => 'fk', 'title' => $report['title'], 'pid' => $node, 'level' => 2, 'display' => 2, 'sid' => ''];
        $top_id = Db::name('softNode')->insertGetId($node_top);
        if (!$top_id) {
            return json(['code' => 1, 'msg' => 'err']);
        }
        return json(['code' => 0, 'msg' => 'success']);
    }
    public function sapi($fun,$Type,$Id){
        $Source = app('app\gadmin\controller\Source');
        $data = $Source->api(str_replace("@","",$fun));
        $ret = json_decode($data->getContent(),true);
        return json(['field'=>$this->get_field($ret['sql']),'Data'=>$ret['data'],'Id'=>$Id,'Type'=>$Type]);
    }
    public function get_field($sql=''){
        $fields = explode(',',$this->get_between($sql, "SELECT ", " FROM `"));
        $real_field = [];
        foreach ($fields as $v){
            if(strpos($v,'`.`') !== false){
                $real_field[] = rtrim((explode('`.`',$v))[1],'`');
            }elseif(strpos($v,' as ') !== false){
                $real_field[] = rtrim((explode(' as ',$v))[1],'`');
            }else{
                $real_field[] = ltrim(rtrim($v,'`'),'`');
            }
        }
        return $real_field;
    }

    /*
     * php截取指定两个字符之间字符串
     * */

    function get_between($input, $start, $end) {

        $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));

        return $substr;

    }
    public function field(){



    }

}
