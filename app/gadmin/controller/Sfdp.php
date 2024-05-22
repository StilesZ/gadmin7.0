<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 * 业务操作
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Datarecycling;
use app\common\server\Widget;
use sfdp\adaptive\Field;
use think\App;
use think\facade\Db;
use sfdp\Api;
use app\common\server\Tpflow;
use app\common\server\Sfdp as Sapi;
use app\common\server\sfdpDesc;
use app\common\server\Initdata;
use think\facade\View;


class Sfdp extends Base
{
    /**
     *列表
     */
    public function index($sid,$supHelp=0)
    {
        $data = (new Api($sid))->sfdpCurd('index', $sid);
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $getData = $this->request->get();
            unset($getData['sid']);
            /*增加前置条件处理*/
            $whereRaw ='';
            if($supHelp>0){
                $whereRaw = Field::getSupRaw($supHelp,$sid);
            }
            $ret_event = GyEvent('CurdEvent',['name_db'=>$data['config']['table'],'act'=>'before_access']);
            if($ret_event['code'] == 1){
               $before_access['before_map'] = $ret_event['msg'];
               $whereRaw = $ret_event['raw'] ?? '';
            }
            $searchMap = array_merge($getData,$postData['search'] ?? [],$before_access ?? []);
            $list = (new Api($sid))->sfdpCurd('GetData', $sid,'',['page'=>$postData['page'],'limit'=>$postData['limit'],'search'=>array_merge(($searchMap ?? []),['uid'=>(string)dataAccess()]),'whereRaw'=>$whereRaw]);
            $listData = $list['list'];
            $jsondata = [];
            $btnArray = $list['list']['field']['btn'];
            $tablename = $list['list']['field']['db_name'];
            $stv = [
                -1 => '<span class="layui-badge-dot layui-bg-red" ></span> 退回',
                0 => '<span class="layui-badge-dot"></span> 保存',
                1 => '<span class="layui-badge-dot layui-bg-green" ></span> 流程',
                2 => '<span class="layui-badge-dot layui-bg-blue" ></span> 通过'
            ];
            foreach ($listData['list'] as $k => $v) {
                $wf = '';//$edit = '<a onclick=edit('.$sid.',{w:"98%",h:"98%"})>edit</a>';
                if (in_array('WorkFlow', $btnArray)) {
                    $wf = \tpflow\Api::wfaccess('btn', ['id' => $v['id'], 'type' => $tablename, 'status' => $v['status']]);
                }
                $listData['list'][$k]['status'] = $stv[$v['status']] ?? 'ERR';
                $listData['list'][$k]['url'] = '<a title='.$v['id'].' onClick=sfdp.openfullpage("查看","' . url('view', ['sid' => $sid, 'id' => $v['id']]) . '") class="btn radius size-S" style="color: #52c41a;">查看</a> '. $wf;
                $ret_event = GyEvent('CurdEvent',['name_db'=>$tablename,'act'=>'list_after','data'=>$listData['list'][$k],'id'=>$sid]);
                $listData['list'][$k]['id'] = '<input type="checkbox" value="' . $v['id'] . '/' . $v['status'] . '/' . $tablename . '" name="ids">';
                if($ret_event['code'] == 1 && is_array($ret_event['msg'])){
                    foreach($ret_event['msg'] as $kk=>$vv){
                        $listData['list'][$k][$kk] = $vv;
                    }
                }
                $jsondata[$k] = $listData['list'][$k];
            }
            return json(["code" => 0, "count" => $list['count'], "msg" => '获取数据成功....', 'data' => $jsondata]);
        }
        $config = $data['config'];
        $style = json_decode($config['sfdp_design']['s_field'],true);
        $btns = Sapi::btnArray($data['btn'], $sid,$style);
        /*添加动作*/
        $ret_event = GyEvent('CurdEvent',['name_db'=>$data['config']['table'],'act'=>'list_before','data'=>['data'=>$data,'btn'=>$btns],'id'=>$sid]);
        if($ret_event['code'] == 1){
            $btns = $ret_event['msg']['btn'] ?? $btns;
            $config = $ret_event['msg']['config'] ?? $config;
        }
        $gbtn = 0;
        if ((in_array('WorkFlow', $data['btn'])) || (in_array('Status', $data['btn']))) {
            $gbtn = 1;
        }
        if ($data['config']['sfdp_design']['s_type']==3) {
            $btns = '<a class="layui-btn layui-btn-sm  layui-btn-primary" onclick=sfdp.openfullpage("新增","' . url('add', ['sid' => $sid,'s_type'=>3]) . '")><i class="layui-icon layui-icon-add-circle-fine"></i> 批量增加</a>'.$btns;
        }
        $sjdata = [];
        if($data['config']['sfdp_design']['s_type']==2){
            $count = Db::name($data['config']['table'])->count();
            $find = Db::name($data['config']['table'])->order('id desc')->find();
            $set = Db::name('sfdp_data')->where('table',$data['config']['table'])->find();
            $sjdata = [$count,date('Y-m-d',$find['create_time']?? time()),$set];
            if(!$set){
               exit('<h2>对不起，您还没有构建表单，请前往业务开发平台——>数据构建！</h2>');
            }
        }
        /*加入用户自定义*/
        $config['sql_field_all'] = $config['sql_field'];$config['field_all'] = $config['field'];
        if($userconfig = (new Api($sid))->sfdpCurd('UserConfig', $sid)){
            $config['sql_field'] = $userconfig['field'];
            $config['field'] = $userconfig['field_name'];
        }
        /*判断是否有列表容器*/

        $widget = Widget::hasSfdp($sid);
        if($widget != false){
            View::assign('ver', g_cache('desktype') ?? 0);
            View::assign($widget);
            View::assign('has_widget', 1);
        }else{
            View::assign('has_widget', 0);
        }
        /*普通列表*/
        if($data['config']['show_type']==0) {
            return view('index', ['sp'=>$supHelp,'config' => $config,'sjdata' => $sjdata, 'btns' => $btns, 'gbtn' => $gbtn, 'sid' => $sid, 'count_field' => $data['config']['count_field'] ?? '']);
        }
        /*树级列表*/
        if($data['config']['show_type']==1){
            $tree = Sapi::tree_data($data['config']['show_fun']);
            return view('index2', ['sp'=>$supHelp,'config' => $config, 'sjdata' => $sjdata,'btns' => $btns, 'gbtn' => $gbtn,'sid' => $sid,'count_field'=>$data['config']['count_field'] ?? '','tree' => json_encode($tree)]);
        }
        /*Tab列表*/
        if($data['config']['show_type']==2) {
            $tab_data = Sapi::tab_data($data['config']['show_fun']);
            return view('index3', ['sp'=>$supHelp,'tab_field'=>$data['config']['show_field'],'tab_data' =>$tab_data,'config' => $config, 'btns' => $btns, 'gbtn' => $gbtn, 'sid' => $sid, 'count_field' => $data['config']['count_field'] ?? '']);
        }
        /*商品图片展示列表*/
        if($data['config']['show_type']==3) {
            return view('index4', ['sp'=>$supHelp,'config' => $config, 'btns' => $btns, 'gbtn' => $gbtn, 'sid' => $sid, 'count_field' => $data['config']['count_field'] ?? '']);
        }
        /*树型列表*/
        if($data['config']['show_type']==4) {
            return view('index5', ['sp'=>$supHelp,'config' => $config,'btns' => $btns, 'gbtn' => $gbtn, 'sid' => $sid, 'count_field' => $data['config']['count_field'] ?? '']);
        }
    }
    /**
     *新增
     */
    public function add($sid,$s_type=0)
    {
        $info = (new Api($sid))->sfdpCurd('info', $sid);
        $s_sys_check = (json_decode($info['data'],true))['s_sys_check'] ?? '0';
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if(!empty($info['nofield']) && $info['nofield'][1] !=''){
                if(in_array($info['nofield'][0],array_keys($data))){
                    $bill_no = self::buildNo($info['nofield'][1]);
                    $bill_no_pre = $bill_no[0].($bill_no[1] ?? '');
                    $no = Db::name($data['name_db'])->where($info['nofield'][0],'like',$bill_no_pre.'%')->order('id desc')->find();
                    if($no){
                        $oldNO = explode($bill_no_pre,$no[$info['nofield'][0]]);
                        $data[$info['nofield'][0]] =$bill_no_pre.str_pad($oldNO['1']+1,$info['nofield'][2],"0",STR_PAD_LEFT);
                    }else{
                        $data[$info['nofield'][0]] =$bill_no_pre.str_pad(1,$info['nofield'][2],"0",STR_PAD_LEFT);
                    }
                }
            }
            return Sapi::Sfdp_save($data,session('softId'),$s_sys_check);/*统一到调用接口*/
        }
        //$add_data = (new Api($sid))->sfdpCurd('add', $sid);
        if($s_type ==3) {
            $desc = Sapi::sfdp_desic_all($info);
            return view('edit2', ['List'=>json_encode($desc['list']),'fieldmast'=>json_encode($desc['fieldmast']),'is_wf'=>Sapi::hasWorkflow($info),'showtype' => 'add', 'config' => $info['config'], 'data' => $info['data']]);
        }else{
            if($info['config']['s_type']==3){
                $info['config']['s_type']=0;
            }
            return view('edit', ['is_wf'=>Sapi::hasWorkflow($info),'showtype' => 'add', 'config' => $info['config'], 'data' => $info['data']]);
        }
    }
    public static function buildNo($str){
        $strArray = explode('$',$str);
        $dataType= ['YMD'=>date('Ymd'),'YM'=>date('Ym'),'Y'=>date('Y'),'YMDhis'=>date('YmdHis'),];
        return [$strArray[0].$dataType[$strArray[1]],$strArray[2]];
    }
    /**
     * 保存全部数据
     */
    public function saveAll(){
        $data = $this->request->post();
        $design = (new Api($data['sid']))->sfdpCurd('show', $data['sid']);
        $desc = Sapi::sfdp_desic_all($design);
        $fields = $desc['fields'];
        foreach($data['data'] as $v){
            if(count($v) != count($fields)){
                return msg_return('数据不一致', 1);
            }
            $post_data = array_combine($fields,$v);
            $post_data['uid'] = session('softId');
            $post_data['create_time'] = time();
            Db::startTrans();
            try{
                $did = Db::name($desc['db'])->insertGetId($post_data);//添加主表
                Sapi::oplog($desc['db'],$did);//添加日志
                Db::commit();
                /*添加后执行的动作*/
                $ret_event = GyEvent('CurdEvent',['name_db'=>$desc['db'],'act'=>'add_after','data'=>$post_data,'id'=>$did]);
                if($ret_event['code'] == 1){
                    return json($ret_event);
                }
            }catch(\Exception $e){
                // 回滚事务
                Db::rollback();
                return msg_return('添加失败'.$e->getMessage(), 1);
            }
        }
        return msg_return('批量写入成功！');
    }

    /**
     * 查看视图
     * @param $id 实体表id
     * @param $sid 关联设计id
     */
    public function view($id, $sid)
    {
        $view = (new Api($sid))->sfdpCurd('view', $sid, $id);
        $view_cont = json_decode($view['info'],true);
        if(in_array('WorkFlow', $view_cont['tpfd_btn'])){
            $wf_log = Tpflow::logData($view_cont['name_db'],$id);
            foreach($wf_log as $k=>$v){
                $wf_log[$k]['name'] = Db::name('soft_user')->where('id',$v['uid'])->value('realname');
            }
            $workflow =$wf_log;
        }else{
            $workflow =[];
        }
        $log = Db::name('soft_oplog')->where('bill_table',$view_cont['name_db'])->where('bill_id',$id)->order('id dsec')->select()->toArray();
        $ret_event = GyEvent('CurdEvent',['name_db'=>$view_cont['name_db'],'act'=>'view_after','data'=>$view,'id'=>$sid]);
        if($ret_event['code'] == 1 && is_array($ret_event['msg'])){
            $view = $ret_event['msg'];
        }
        $view['config']['hasprint'] = Db::name('soft_print')->where('sid',$sid)->where('type',g_cache('print'))->value('id') ?? '0';
        return view('view', ['info' => $view['info'],'st' => ($view['i']['row']['status'] ?? 0),'wf'=>$workflow,'log'=>$log,'table'=>$view_cont['name_db'], 'config' => $view['config'], 'sid' => $sid, 'id' => $id,'linkdata'=>$view['l']]);
    }
    public function linkdata($sid,$wRaw='',$wField='',$bid='')
    {
        $data = (new Api($sid))->sfdpCurd('index', $sid);
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $getData = $this->request->get();
            $wRaw = input('wRaw');
            unset($getData['sid']);
            /*增加前置条件处理*/
            $whereRaw ='';
            $ret_event = GyEvent('CurdEvent',['name_db'=>$data['config']['table'],'act'=>'before_access']);
            if($ret_event['code'] == 1){
                $before_access['before_map'] = $ret_event['msg'];
                $whereRaw = $ret_event['raw'] ?? '';
            }
            if($wRaw != ''){
                $wRaw =  '('.$wRaw.')and('.$wField.'='.$bid.')';
            }else{
                $wRaw =  '('.$wField.'='.$bid.')';
            }
            if($whereRaw != ''){
                $whereRaw = '('.$whereRaw.') and '.$wRaw;
            }else{
                $whereRaw = $wRaw;
            }
            $searchMap = array_merge($getData,$postData['search'] ?? [],$before_access ?? []);
            $list = (new Api($sid))->sfdpCurd('GetData', $sid,'',['page'=>$postData['page'],'limit'=>$postData['limit'],'search'=>array_merge(($searchMap ?? []),['uid'=>(string)dataAccess()]),'whereRaw'=>$whereRaw]);
            $listData = $list['list'];
            $jsondata = [];
            $btnArray = $list['list']['field']['btn'];
            $tablename = $list['list']['field']['db_name'];
            $stv = [
                -1 => '<span class="layui-badge-dot layui-bg-red" ></span> 退回',
                0 => '<span class="layui-badge-dot"></span> 保存',
                1 => '<span class="layui-badge-dot layui-bg-green" ></span> 流程',
                2 => '<span class="layui-badge-dot layui-bg-blue" ></span> 通过'
            ];
            foreach ($listData['list'] as $k => $v) {
                $wf = '';//$edit = '<a onclick=edit('.$sid.',{w:"98%",h:"98%"})>edit</a>';
                if (in_array('WorkFlow', $btnArray)) {
                    $wf = \tpflow\Api::wfaccess('btn', ['id' => $v['id'], 'type' => $tablename, 'status' => $v['status']]);
                }
                $listData['list'][$k]['status'] = $stv[$v['status']] ?? 'ERR';
                $listData['list'][$k]['url'] = '<a title='.$v['id'].' onClick=sfdp.openfullpage("查看","' . url('view', ['sid' => $sid, 'id' => $v['id']]) . '") class="btn radius size-S" style="color: #52c41a;">查看</a> '. $wf;
                $ret_event = GyEvent('CurdEvent',['name_db'=>$tablename,'act'=>'list_after','data'=>$listData['list'][$k],'id'=>$sid]);
                $listData['list'][$k]['id'] = '<input type="checkbox" value="' . $v['id'] . '/' . $v['status'] . '/' . $tablename . '" name="ids">';
                if($ret_event['code'] == 1 && is_array($ret_event['msg'])){
                    foreach($ret_event['msg'] as $kk=>$vv){
                        $listData['list'][$k][$kk] = $vv;
                    }
                }
                $jsondata[$k] = $listData['list'][$k];
            }
            return json(["code" => 0, "count" => $list['count'], "msg" => '获取数据成功....', 'data' => $jsondata]);
        }
        $config = $data['config'];
        $style = json_decode($config['sfdp_design']['s_field'],true);
        $btns = Sapi::btnArray($data['btn'], $sid,$style);
        /*添加动作*/
        $ret_event = GyEvent('CurdEvent',['name_db'=>$data['config']['table'],'act'=>'list_before','data'=>['data'=>$data,'btn'=>$btns],'id'=>$sid]);
        if($ret_event['code'] == 1){
            $btns = $ret_event['msg']['btn'] ?? $btns;
            $config = $ret_event['msg']['config'] ?? $config;
        }
        $gbtn = 0;
        if ((in_array('WorkFlow', $data['btn'])) || (in_array('Status', $data['btn']))) {
            $gbtn = 1;
        }
        if ($data['config']['sfdp_design']['s_type']==3) {
            $btns = '<a class="layui-btn layui-btn-sm  layui-btn-primary" onclick=sfdp.openfullpage("新增","' . url('add', ['sid' => $sid,'s_type'=>3]) . '")><i class="layui-icon layui-icon-add-circle-fine"></i> 批量增加</a>'.$btns;
        }
        $sjdata = [];
        if($data['config']['sfdp_design']['s_type']==2){
            $count = Db::name($data['config']['table'])->count();
            $find = Db::name($data['config']['table'])->order('id desc')->find();
            $set = Db::name('sfdp_data')->where('table',$data['config']['table'])->find();
            $sjdata = [$count,date('Y-m-d',$find['create_time']?? time()),$set];
            if(!$set){
                exit('<h2>对不起，您还没有构建表单，请前往业务开发平台——>数据构建！</h2>');
            }
        }
        return view('', ['config' => $config,'sjdata' => $sjdata, 'btns' => $btns, 'gbtn' => $gbtn, 'sid' => $sid, 'count_field' => $data['config']['count_field'] ?? '']);
    }

    /**
     *删除
     */
    public function del($id, $sid,$table)
    {
        /*添加前执行的动作*/
        $ret_event = GyEvent('CurdEvent',['act'=>'del_fun','id'=>$id,'name_db'=>$table]);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        /*判断是否开启数据回收站*/
        if(g_cache('datarecycling')==1){
            /*判断下子表的数据*/
            $datar = (new Api($sid))->sfdpCurd('add', $sid);
            $datar = count(json_decode($datar['data'],true)['sublist']);
            $main_data = Db::name($table)->find($id);//主表数据
            $sub_data = [];
            for ($i=1; $i<=$datar; $i++)
            {
                $sub_data[$i] = Db::name($table.'_d'.$i)->where('d_id',$id)->select()->toArray();//主表数据
            }
            $datarecycling = [
                'table'=>$table,
                'table_id'=>$id,
                'table_data'=>json_encode(['main'=>$main_data,'sub'=>$sub_data]),
            ];
            if(!Datarecycling::add($datarecycling)){
                return msg_return('数据回收失败，系统错误，请联系管理员！！！！', 1);
            }
        }
        $view = (new Api($sid))->sfdpCurd('del', $sid, $id);
        if ($view) {
            Sapi::oplog($table,$id,2);
            return msg_return('删除成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }
    /**
     *状态修改
     */
    public function status($id, $table, $status)
    {
        $ret = Sapi::status($id, $table, $status);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }
    /**
     * 提供工作流修改调用的接口信息
     */
    public function wfedit($id, $sid)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            return Sapi::Sfdp_edit($data,$id,1);//统一调整为服务接口
        }
        $data = (new Api($sid))->sfdpCurd('edit', $sid, $id);
        if($data['config']['s_type']==3){
            $data['config']['s_type']=0;
        }
        return view('edit', ['is_wf'=>Sapi::hasWorkflow($data),'showtype' => 'edit', 'config' => $data['config'], 'data' => $data['data'], 'id' => $id]);
    }
    /**
     *修改
     */
    public function edit($id, $sid)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            return Sapi::Sfdp_edit($data,$id);//统一调整为服务接口
        }
        $data = (new Api($sid))->sfdpCurd('edit', $sid, $id);
        if($data['config']['s_type']==3){
            $data['config']['s_type']=0;
        }
        $s_sys_edit = (json_decode($data['data'],true))['s_sys_edit'] ?? '0';
        if($s_sys_edit==2 && $data['bill_info']['uid']!=session('softId')){
            return '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("对不起，您无权编辑此表单!");setTimeout("parent.layer.close(index)",1000);</script>';
        }
        if($s_sys_edit==1){
            return '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("对不起，当前表单禁用编辑!");setTimeout("parent.layer.close(index)",1000);</script>';
        }
        $name_db = (json_decode($data['data'],true))['name_db'];
        /*修改前执行的动作*/
        $ret_event = GyEvent('CurdEvent',['name_db'=>$name_db,'act'=>'edit_before','id'=>$id,'data'=>$data,'post'=>'view']);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        return view('edit', ['is_wf'=>Sapi::hasWorkflow($data),'showtype' => 'edit', 'config' => $data['config'], 'data' => $data['data'], 'id' => $id]);
    }
    /**
     *导入信息
     */
    public function import($sid){
        if ($this->request->isPost()) {
            $data = input('csv');
            $sfdp_ver_id = Db::name('sfdp_design_ver')->where('sid',$sid)->where('status',1)->find();
            $header_data = Db::name('sfdp_field')->where('sid',$sfdp_ver_id['id'])->where('field','not in','id,create_time,update_time')->column('field,name_type,name');
            $header =[];
            foreach($header_data as $v){
                $header[] = $v["field"];
            }
            foreach($data as $k=>$v) {
                $csv[] = explode(',',$v);
            }
            unset($csv[0]);//删除标题行
            if(count($csv)>0 && count($csv[1])==count($header)){
                $dataArray = [];
                foreach($csv as $k=>$v){
                    foreach($header as $k2=>$v2){
                        $dataArray[$k][$v2] = $v[$k2];
                    }
                    $dataArray[$k]['create_time'] = time();
                    $dataArray[$k]['update_time'] = time();
                    /*导入后执行的动作*/
                    $ret_event = GyEvent('CurdEvent',['name_db'=>input('table'),'act'=>'import_fun','data'=>$dataArray[$k]]);
                    if($ret_event['code'] == 1 && is_array($ret_event['msg'])){
                        foreach($ret_event['msg'] as $kk=>$vv){
                            $dataArray[$k][$kk] = $vv;
                        }
                    }
                }
            }else{
                return json(['code'=>1,'msg'=>'数据列不匹配，请勿修改模板']);
            }
            try{
                $ret = Db::name($sfdp_ver_id["s_db"])->insertAll($dataArray);
            }catch(\Exception $e){
                return json( ['code'=>-1,'msg'=>$e->getMessage()]);
            }
            if($ret == count($data)){
                return json( ['code'=>0,'msg'=>'导入成功一共导入【'.$ret.'】！']);
            }else{
                return json( ['code'=>1,'msg'=>'导入失败一共导入【'.$ret.'】！']);
            }
        }
        $data = (new Initdata())->down($sid,2);
        return view('', $data);
    }
    public function imp(){
        return view();
    }

    public function __call($method, $args)
    {
        $extensionClass = "app\\common\\sfdpextend\\sfdp{$args['sid']}";
        if (class_exists($extensionClass)) {
            $extensionInstance = new $extensionClass();
            if (method_exists($extensionInstance, $method)) {
                return call_user_func([$extensionInstance, $method],$args);
            }
        }else{
            echo '404';
        }
    }

    /*业务设计器引入*/
    use sfdpDesc;
}
