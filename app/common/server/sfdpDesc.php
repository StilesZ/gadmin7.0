<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 * 超级表单二次开发
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;
use sfdp\Api;
use sfdp\lib\unit;
use tpflow\adaptive\Flow;

trait sfdpDesc
{
    /**
     * 帮助系统模块查看调用接口
     * @param $sid 业务关联的Sid
     */
    public function help_view($sid){
        echo Help::helpFind($sid);
    }
    /**
     * 帮助系统模块查看调用接口
     * @param $sid 业务关联的Sid
     */
    public function print_view($sid,$id){
        if(g_cache('print')==1) {
            $print = Db::name('soft_print')->where('sid',$sid)->find();
            if(!$print){
                return g_returnJSMsg('未设计打印模板,系统自动关闭!');
            }
            $info = Help::PrintData($print,$sid,$id);
            return view('print3',['sid'=>$sid,'data'=>json_encode($info),'con'=>Help::helpPrint($sid)]);
        }else{
            echo Help::PrintView($sid,$id);
        }
    }

    /**
     * 帮助系统设计
     * @param $sid
     */
    public function help($sid){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Help::helpAdd($data);
            if(!$ret){
                return msg_return('操作失败！', 1);
            }
            return msg_return('帮助设计成功！！');
        }
        $data = Db::name('sfdp_design')->where('s_design',2)->find($sid);
        if(!$data){
            return g_returnJSMsg('未部署版本,系统自动关闭!');
        }
        return view('help',['sid'=>$sid,'con'=>Help::helpFind($sid),'data'=>$data]);
    }
    /**
     * 打印模板
     * @param $sid
     */
    public function print($sid){

        if($this->request->isPost()){
            $data = $this->request->post();
            $ret = Help::PrintAdd($data);
            if(!$ret){
                return msg_return('操作失败！', 1);
            }
            return msg_return('打印设计成功！！');
        }
        $design_ver = Db::name('sfdp_design_ver')->where('sid',$sid)->where('status',1)->find();
        if(!$design_ver){
            return g_returnJSMsg('未部署版本,系统自动关闭!');
        }
        $view_cont = json_decode($design_ver['s_field'],true);
        if(in_array('WorkFlow', $view_cont['tpfd_btn'])){
            $wf =1;
        }
        $sfdp_field = Db::name('sfdp_field')->where('sid',$design_ver['id'])->field('field,name')->select();
        if(g_cache('print')==1){
            return view('print2',['sid'=>$sid,'con'=>Help::helpPrint($sid),'field'=>$sfdp_field,'data'=>$design_ver]);
        }else{
            return view('print',['sid'=>$sid,'con'=>Help::helpPrint($sid),'field'=>$sfdp_field,'data'=>$design_ver,'opt'=>[$wf ?? 0,count($view_cont['sublist'])]]);
        }
    }

    /**
     * 事件管理
     * @param $sid
     */
    public function data($sid){
        if($this->request->isPost()){
            $data = $this->request->post();
            $ret =  (new Api($sid))->sfdpCurd('Data',$sid,$data);
            if($ret['code']==1){
                return msg_return($ret['msg'], 1);
            }
            return msg_return('保存成功！！');
        }
        $design_ver = Db::name('sfdp_design_ver')->where('sid',$sid)->where('status',1)->find();
        if(!$design_ver){
            return g_returnJSMsg('未部署版本,系统自动关闭!');
        }
        $row = Db::name('sfdp_data')->where('sid',$sid)->find();
        return view('data',['row'=>$row,'sid'=>$sid,'data'=>$design_ver]);
    }

    /**
     * 事件管理
     * @param $sid
     */
    public function event($sid){
        if($this->request->isPost()){
            $data = $this->request->post();
            $ret = Event::Add($data);
            if($ret['code']==1){
                return msg_return($ret['msg'], 1);
            }
            return msg_return('保存成功！！');
        }
        $design_ver = Db::name('sfdp_design_ver')->where('sid',$sid)->where('status',1)->find();
        return view('event',['sid'=>$sid,'data'=>$design_ver]);
    }

    public function sfdp_source($sid){
        $design_ver = Db::name('sfdp_design_ver')->where('sid',$sid)->where('status',1)->find();
        $sfdp_field = Db::name('sfdp_field')->where('sid',$design_ver['id'])->column('field');
        $is_name = in_array('name',$sfdp_field);
        $is_title = in_array('title',$sfdp_field);
        if($is_name || $is_title){
            if($is_title){
               $field = 'id,title as name';
            }
            if($is_name){
                $field = 'id,name';
            }
            if(Db::name('soft_source')->where('fun','get_'.$design_ver['s_db'].'_list')->find()){
                return msg_return('对不起，方法已经存在', 1);
            }
            $api = [
                'title'=>'获取'.$design_ver['s_name'].'列表信息',
                'fun'=>'get_'.$design_ver['s_db'].'_list',
                'table'=>'g_'.$design_ver['s_db'],
                'conn'=>'mysql',
                'type'=>0,
                'field'=>$field,//重点
                'order'=>'',
                'group'=>'',
                'add_time'=>date('Y-m-d H:i:s'),
                'join'=>'',
                'add_name'=>session('sfotUserName'),
                'status'=>0,
                'where'=>'',
                'stype'=>'1',
                'customsql'=>'',
            ];
            $ret = Db::name('soft_source')->insertGetId($api);
            $api['title'] = '获取'.$design_ver['s_name'].'的详细信息';
            $api['fun'] = 'get_'.$design_ver['s_db'].'_byid';
            $api['field'] = '*';
            $api['type'] = 2;
            $api['where'] = 'id=:id';
            $ret = Db::name('soft_source')->insertGetId($api);
            if ($ret) {
                return msg_return('创建成功！');
            } else {
                return msg_return($ret, 1);
            }
        }else{
            return msg_return('设计库中，不包含name,title等字段，您需要手动创建！', 1);
        }
    }
    public function sfdp_reset($sid){
        $design_ver = Db::name('sfdp_design')->where('id',$sid)->find();
        $table = $design_ver['s_db'];
        Db::startTrans();
        try {
            Db::name('soft_oplog')->where('bill_table',$table)->delete();//删除日志记录
            $run_ids = Db::name('wf_run')->where('from_table',$table)->column('id');//找出全部运行id
            Db::name('wf_run')->where('from_table',$table)->delete();//删除运行日志
            Db::name('wf_run_log')->where('from_table',$table)->delete();//删除流程日志
            Db::name('wf_run_process')->where('run_id','in',$run_ids)->delete();//删除运行步骤日志
            Db::name('wf_run_process_cc')->where('from_table',$table)->delete();//删除抄送日志
            Db::commit();
            return msg_return('删除成功！');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return msg_return('删除失败！','-1');
        }
    }
    /**
     * 事件查询
     * @param $sid
     */
    public function event_find($sid,$act){
        return json(Event::find($sid,$act));
    }

    /**
     * Sfdp设计业务列表
     * @param array $map
     */
    public function sfdpList($map = []){
        if (input("s_title")){
            $map[] = ['s_title', 'like', '%'.input('s_title').'%'];
        }
        if (input("s_db")){
            $map[] = ['s_db', 'like', '%'.input('s_db').'%'];
        }
        if (input("sids")||input("sids")=='0'){
            $map[] = ['id', 'in', input("sids")];
        }
        $listData = (new Api())->sfdpApi('listData',null,$map);
        $urls= unit::gconfig('url');
        if(unit::gconfig('node_mode')==2){
            $className = unit::gconfig('node_action');
            if(!class_exists($className)){
                return 'Sorry,未找到node_action类，请先配置~';
            }
            $Node = (new $className())->GetNode();//获取目录节点信息
        }else{
            $Node = unit::gconfig('node_data');
        }
        foreach($listData as $k=>$v){
            $field = json_decode($v['s_field'],true);
            if(isset($field['tpfd_btn']) && in_array('WorkFlow',$field['tpfd_btn'])){
                $listData[$k]['is_wf'] =1;
            }else{
                $listData[$k]['is_wf'] =0;
            }
        }
        return view('sfdp_list',['list'=>$listData,'urls'=>$urls['api'],'Node'=>$Node,'tree'=>json_encode(g_generateTree($Node['tree']))]);
    }

    /**
     * 对接tpflow 设计器接口
     * @param $db  表名
     * @return \think\response\View
     */
    public function sfdp_wf($db){
        $data = Flow::GetFlow(['type'=>$db],1,99999);
        $urls = \tpflow\lib\unit::gconfig('wf_url');
        $url = [
            'edit'=>$urls['wfapi'] . '?act=add&id=',
            'desc'=>$urls['designapi'] . '?act=wfdesc&flow_id=',
            'status'=>$urls['wfapi'] . '?act=add',
            'del'=>$urls['wfapi'] . '?act=del'
        ];
        return view('sfdp_wf', ['data' => $data,'url'=>$url]);
    }

    /**
     * 创建业务
     *
     */
    public function sfdp_add(){
        if($this->request->isPost()){
            $data = $this->request->post();
            $table = config('database.connections.mysql.prefix').$data['s_db'];
            $exist = Db::query('show tables like "'.$table.'"');
            if($exist){
                return msg_return('数据表已经存在，请修改！', 1);
            }//
            $s_field ='{"name":"'.$data['s_title'].'","name_db":"'.$data['s_db'].'","tpfd_id":"SFDP'.date('Ymdhmsi').'","tpfd_btn":'.json_encode($data['tpfd_btn']).',"tpfd_script":"'.$data['tpfd_script'].'","tpfd_class":"'.$data['tpfd_class'].'","tpfd_del":"'.$data['s_del'].'","tpfd_saas":"'.$data['s_saas'].'","tpfd_style":"'.$data['s_form_style'].'","tpfd_open":"'.$data['s_open'].'","s_sys_check":"'.$data['s_sys_check'].'","s_sys_edit":"'.$data['s_sys_edit'].'","list":{},"sublist":{},"tpfd_content":"","tpfd_time":"'.date('Y-m-d h:i:s').'","tpfd_ver":"v6.0"}';
            $id = Db::name('sfdp_design')->insertGetId([
                's_bill'=>unit::OrderNumber(),
                's_title'=>$data['s_title'],
                's_db'=>$data['s_db'],
                's_field'=>$s_field,
                'add_user'=>session('sfotUserName'),
                'add_time'=>time(),
                's_type'=>$data['s_type'],
                's_search'=>[],
                's_list'=>[],
                's_design'=>1
            ]);
            if($id){
                $className = unit::gconfig('node_action');
                $Node = (new $className())->SaveNode($id,['btn'=>$data['tpfd_btn'],'db_name'=>$data['s_db'],'title'=>$data['s_title']],$data['mid']);//获取目录节点信息
                if($Node['code']==0){
                    return msg_return('保存成功！！');
                }else{
                    return msg_return($Node['msg'], 1);
                }
            }else{
                return msg_return('创建业务失败', 1);
            }
        }
        if(unit::gconfig('node_mode')==2){
            $className = unit::gconfig('node_action');
            if(!class_exists($className)){
                return 'Sorry,未找到node_action类，请先配置~';
            }
            $Node = (new $className())->GetNode();//获取目录节点信息
        }else{
            $Node = unit::gconfig('node_data');
        }
        return view('', ['Node' => $Node]);
    }
    /**
     * 删除设计信息
     *
     */
    public function sfdp_del($id){
        /*删除设计信息*/
        Db::startTrans();
        try {
            Db::name('sfdp_design')->where('id',$id)->delete();
            $ver_ids = Db::name('sfdp_design_ver')->where('sid',$id)->column('id');
            Db::name('sfdp_design_ver')->where('sid',$id)->delete();
            Db::name('sfdp_field')->where('sid','in',$ver_ids)->delete();
            Db::name('sfdp_modue')->where('sid','in',$ver_ids)->delete();
            Db::name('soft_node')->where('sid',$id)->delete();
            Db::commit();
            return msg_return('删除成功！');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return msg_return('删除失败！','-1');
        }
    }
    public function sfdp_field($sid){
       $data =  (new Api())->sfdpApi('field',$sid);
        return view('', ['data' => $data]);
    }
    /**
     * 桌面构建
     */
    public function sfdp_build($sid,$type){
        $res = Desk::buildDesk($sid,$type);
        if($res){
            return msg_return('创建成功！！');
        }else{
            return msg_return('创建失败', 1);
        }
    }

    public function no(){
        if ($this->request->isPost()) {
            $act= input('post.act') ?? 1;
            if($act=='del'){
                Db::name('sfdp_billno')->delete(input('post.id'));
                return json(['code' => 0,'msg' => 'Success']);
            }

           $post= input('post.json');
           $json_data = json_decode($post,true);
           foreach($json_data as $k=>$v){
               if(isset($v['id'])){
                   unset($v['tempId'],$v['LAY_TABLE_INDEX'],$v['LAY_NUM'],$v['LAY_INDEX']);
                   $v['update_time'] = time();
                   Db::name('sfdp_billno')->update($v);
               }else{
                   $hasno =  Db::name('sfdp_billno')->where('type',$v['type'])->find();
                   if(!$hasno) {
                       unset($v['tempId'],$v['LAY_TABLE_INDEX'],$v['LAY_NUM'],$v['LAY_INDEX']);
                       $v['update_time'] = time();
                       Db::name('sfdp_billno')->insertGetId($v);
                   }
               }
           }
            return json(['code' => 0,'msg' => 'Success']);
        }
        $billno =  Db::name('sfdp_billno')->field('*,id as tempId')->select()->toArray();

       $data =  Db::name('sfdp_design')->where('s_design',2)->field('id,CONCAT(s_bill,"-",s_title,"-",s_db) as name')->select()->toArray();
        return view('', ['data' => json_encode($data,true),'billno' => json_encode($billno,true)]);
    }

    /**
     * 列表容器构建
     * @param $sid
     */
    public function sfdp_container($sid){
        if ($this->request->isPost()) {
            $data = input('post.');
            if(!isset($data['data'])){
                $ret = Db::name('sfdp_widget')->where('sid', $sid)->delete();
                if ($ret) {
                    return msg_return('操作成功');
                } else {
                    return msg_return('操作失败', 1);
                }
            }
            $w['widget'] = implode(',',$data['data']);
            $w['sid'] = $sid;
            $w['uptime'] = time();
            if (Db::name('sfdp_widget')->where('sid', $sid)->find()) {
                $ret = Db::name('sfdp_widget')->where('sid', $sid)->update($w);
            }else {
                $ret = Db::name('sfdp_widget')->insertGetId($w);
            }
            if ($ret) {
                return msg_return('操作成功');
            } else {
                return msg_return('操作失败', 1);
            }
        }
        $WidgetUser = Db::name("sfdp_widget")->where('sid', $sid)->value('widget');
        $ids = explode(',',$WidgetUser ?? '');
        foreach($ids as $k=>$v){
            $has = Db::name('soft_widget')->find($v);
            if(!$has){
                unset($ids[$k]);
            }
        }
        $WidgetUser = implode(',',$ids);
        return view('', ['sid'=>$sid,'widgets'=>Widget::data(1),'selected' => explode(',',$WidgetUser), 'WidgetUser' => $WidgetUser]);
    }

    /**
     * 生成SFDP拓展包
     */
    public function extend_plug($id){
        $bill = unit::OrderNumber();
        $sql =[];
        $data = Db::name('sfdp_design')->where('id', $id)->find();
        if ($data) {
            $sql[] = self::data_to_sql('sfdp_design',$data,'',$bill);
        }
        //找出脚本表
        $data3 = Db::name('sfdp_billno')->where('type', $id)->find();
        if ($data3) {
            $sql[] = self::data_to_sql('sfdp_billno',$data3,'type',$bill);
        }
        //找出编码规则表
        $data4 = Db::name('sfdp_field_index')->where('sid', $id)->find();
        if ($data4) {
            $sql[] = self::data_to_sql('sfdp_field_index',$data4,'sid',$bill);
        }

        $data5 = Db::name('sfdp_script')->where('sid', $id)->find();
        if ($data5) {
            $sql[] = self::data_to_sql('sfdp_script',$data5,'sid',$bill);
        }
        $data6 = Db::name('soft_event')->where('sid', $id)->select()->toArray();
        foreach ($data6 as $k=>$v){
            $sql[] = self::data_to_sql('soft_event',$v,'sid',$bill);
        }
        $data7 = Db::name('soft_help')->where('sid', $id)->find();
        if ($data7) {
            $sql[] = self::data_to_sql('soft_help',$data7,'sid',$bill);
        }
        $data8 = Db::name('soft_print')->where('sid', $id)->find();
        if ($data8) {
            $sql[] = self::data_to_sql('soft_print',$data8,'sid',$bill);
        }
        // 设置文件名
        $filename = $data['s_title'].'-'.$bill."sfdp.sql";
        $filepath = root_path() . 'public' . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($filepath, implode('',$sql));
        header("Content-Type: text/plain");
        header("Content-Length: " . filesize($filepath));
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($filepath);
        unlink($filepath);
    }

    static function data_to_sql($table,$data,$link,$bill){
        $fields = implode(',', array_map(function ($field) {
            return "`$field`";
        }, array_keys($data)));
        if($link=='type'){
            $data['type'] = '@lastId';
        }
        if($link=='sid'){
            $data['sid'] = '@lastId';
        }
        if($table== 'sfdp_design' || $table=='sfdp_script'){
            $data['s_bill'] = $bill;
        }
        if($table== 'soft_print' && !isset($data['uptime'])){
            $data['uptime'] = time();
        }
        $values = implode(',', array_map(function ($value, $field) {
            if($field=='id'){
                return 'NULL';
            }
            if($value=='@lastId'){
                return $value;
            }
            return "'".addslashes($value ?? '')."'";
        }, $data, array_keys($data)));
        if($table=='sfdp_design'){
            return "INSERT INTO g_$table ($fields) VALUES ($values);SET @lastId = LAST_INSERT_ID();";;
        }
        return "INSERT INTO g_$table ($fields) VALUES ($values);";;

    }

}
