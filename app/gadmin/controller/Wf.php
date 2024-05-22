<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use think\facade\Db;
use app\common\server\Tpflow;
use tpflow\adaptive\Flow;
use tpflow\adaptive\Info;
use tpflow\exception\FlowException;
use tpflow\lib\lib;
use tpflow\lib\unit;

class Wf extends Base
{
    public function wfmy($type=0){
        if ($this->request->isPost()) {
            if($type==3){
                $map = [];
                $postData = $this->request->post();
                if(input('keyword')){
                    $map[]=['w.flow_name','like','%'.input('keyword').'%'];
                }
                $mydata = Tpflow::wfMysend($postData['page'],$postData['limit'],$map);
                foreach($mydata['data'] as $k=>$v){
                    $urls = str_replace(["wfedit"],'view',$v['urls']);
                    $view = '<span style="color: #52c41a;" class="btn" onclick=Tpflow.lopen("查看","'.$urls.'",100,100)>查看</span>';
                    if($v['status']==1){
                        $mydata['data'][$k]['btn'] = $view.' <span class="layui-badge" >流程已结束</span>';
                    }else{
                        $mydata['data'][$k]['btn'] = $view.' <span class="layui-badge layui-bg-blue" >流程未结束</span>';
                    }
                }
                return json(['code' => 0, 'count' => $mydata['count'], 'data' => $mydata['data'], 'msg' => '']);
            }
            /*我的会签*/
            if($type==4){
                $map = [];
                $postData = $this->request->post();
                if(input('keyword')){
                    $map[]=['w.flow_name','like','%'.input('keyword').'%'];
                }
                $mydata = Tpflow::mySing($postData['page'],$postData['limit'],$map);
                foreach($mydata['data'] as $k=>$v){
                    $urls = str_replace(["wfedit"],'view',$v['urls']);
                    $view = '<span style="color: #52c41a;" class="btn" onclick=Tpflow.lopen("查看","'.$urls.'",100,100)>查看</span>';
                    if($v['is_agree']==1){
                        $mydata['data'][$k]['btn'] = $view.' <span class="layui-badge" >流程已结束</span>';
                    }else{
                        $mydata['data'][$k]['btn'] = $view.' <span class="layui-badge layui-bg-blue" >流程未结束</span>';
                    }
                }
                return json(['code' => 0, 'count' => $mydata['count'], 'data' => $mydata['data'], 'msg' => '']);
            }
            $map[]=['f.status','=',$type];
            if($type==0){
                $map[]=['r.status','=',0];
            }
            if(input('keyword')){
                $map[]=['w.flow_name','like','%'.input('keyword').'%'];
            }
            $postData = $this->request->post();

                $errMsg = '';
                $mydata = Tpflow::getWork($map,$postData['page'],$postData['limit']);

                foreach($mydata['data'] as $k=>$v){
                    try {
                    if($type==0){
                        $view = '<span style="color: #52c41a;" class="btn" onclick=Tpflow.lopen("查看","'.$v['urls'].'",100,100)>查看</span>';
                        $mydata['data'][$k]['btn'] =$view . \tpflow\Api::wfaccess('btn', ['id' => $v['from_id'], 'type' => $v['from_table'], 'status' => 1]) ;
                    }else{
                        $urls = str_replace(["wfedit"],'view',$v['urls']);
                        $view = '<span style="color: #52c41a;" class="btn" onclick=Tpflow.lopen("查看","'.$urls.'",100,100)>查看</span>';
                        $mydata['data'][$k]['btn'] = $view;
                    }
                    }catch(FlowException $e){
                        $mydata['data'][$k]['btn'] = '';
                        $data = $e->getData();
                        $errMsg .= '流程异常:'.$e->getMessage().',流程编号:'.$data['run_id'].';步骤编号：'.$data['process_id'].';表单:'.$data['from_table'].'<br>';
                    }
                }

            return json(['code' => 0, 'count' => count($mydata['data']), 'data' => $mydata['data'], 'msg' => '','errMsg'=>$errMsg]);
        }
        $year = Db::name('wf_kpi_year')->where('k_year',date('Y'))->field('k_uid,k_mark,k_time,k_mark/k_time as px')->order('px asc')->limit(7)->select();
        $month = Db::name('wf_kpi_month')->where('k_year',date('Y'))->where('k_month',date('m'))->field('k_uid,k_mark,k_time,k_mark/k_time as px')->order('px asc')->limit(7)->select();
        $ysend = Db::name('wf_kpi_data')->where('k_year',date('Y'))->where('k_node','node-start')->field('k_uid,count(k_uid) as px')->order('px desc')->limit(7)->group('k_uid')->select();
        return view('',['year'=>$year,'month'=>$month,'ysend'=>$ysend]);
    }
    //流程监控
    public function wfctrl(){
      $data = Info::worklist();
        $urls = unit::gconfig('wf_url');
      foreach($data as $k=>$v){
          $data[$k]['btn'] ='<a  onclick=Tpflow.wfconfirm("' . $urls['wfapi'] . '?act=wfend",{"id":' . $v['id'] . '},"您确定要终止该工作流吗？");>终止</a>  |  ' . lib::tpflow_btn($v['from_id'], $v['from_table'], 100,'');
      }
      $year = Db::name('wf_kpi_year')->where('k_year',date('Y'))->field('k_uid,k_mark,k_time,k_mark/k_time as px')->order('px asc')->limit(7)->select();
      $month = Db::name('wf_kpi_month')->where('k_year',date('Y'))->where('k_month',date('m'))->field('k_uid,k_mark,k_time,k_mark/k_time as px')->order('px asc')->limit(7)->select();
      $ysend = Db::name('wf_kpi_data')->where('k_year',date('Y'))->where('k_node','node-start')->field('k_uid,count(k_uid) as px')->order('px desc')->limit(7)->group('k_uid')->select();
      return view('wfctrl', ['data' => $data,'year'=>$year,'month'=>$month,'ysend'=>$ysend]);
    }
	public function index($page = 1, $limit = 10, $draw = 1, $map = []){
		$type = [];
		foreach (Info::get_wftype() as $k => $v) {
			$type[$v['name']] = str_replace('[work]', '', $v['title']);;
		}
		$urls = unit::gconfig('wf_url');
		if ($this->request->isPost()) {
			if (input("type")) $map[] = ['flow_name|type', 'like', '%' . input('type') . '%'];
			$data = Flow::GetFlow($map,$page,(int)$limit);
			$List= $data['rows'];
			$status = ['正常', '禁用'];
			$is_field = ['关闭', '开启'];
			foreach ($List as $k => $v) {
				$List[$k]['type'] = ($type[$v['type']] ?? 'ERR').'-'.$v["type"];
				$List[$k]['is_field'] = $is_field[$v['is_field']] ?? 'ERR';
				$List[$k]['add_time'] = date('Y-m-d',$v['add_time']);
                $url_view = $urls['wfapi'] . '?act=view&id=' . $v['id'];
				if ($v['edit'] == '') {
					$url_edit = $urls['wfapi'] . '?act=add&id=' . $v['id'];
					$url_desc = $urls['designapi'] . '?act=wfdesc&flow_id=' . $v['id'];
					$btn = "<a class='btn' onclick=Tpflow.lopen('修改','" . $url_edit . "','55','60')> 修改</a> <a class='btn' onclick=window.parent.openUrl('".$List[$k]['type']."设计','" . $url_desc . "')> 设计</a> ";
				} else {
					$btn = "<a class='btn'> 运行中.. </a>";
				}
				if ($v['status'] == 0) {
					$btn .= "<a class='btn' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=add' . "',{'id':" . $v['id'] . ",'status':1},'您确定要禁用该工作流吗？') style='color: red'> 禁用</a>";
				} else {
					$btn .= "<a class='btn' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=add' . "',{'id':" . $v['id'] . ",'status':0},'您确定要启用该工作流吗？')> 启用</a>";
				}
                $btn .= " <a class='btn' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=ver' . "',{'id':" . $v['id'] . "},'您确定复刻新流程吗？')> 版本+</a>";
                $btn .= " <a class='btn' onclick=Tpflow.lopen('流程图','" . $url_view . "','55','60')> 流程图</a>";
                $btn .= " <a class='btn' onclick=Tpflow.wfconfirm('" . $urls['wfapi'] . '?act=del' . "',{'id':" . $v['id'] . "},'您确定要删除该工作流吗？') style='color: #fac20a'> 删除</a>";
				$List[$k]['status'] = $status[$v['status']] ?? 'ERR';
				unset($List[$k]['edit']);
				$List[$k]['btn'] = $btn;
			}
            return json(['code' => 0, 'count' => $data['total'], 'data' => $List, 'msg' => '']);
		}
		
		return view('index',['url'=>$urls]);
		
	}
	
}
