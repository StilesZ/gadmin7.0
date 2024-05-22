<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;
use think\facade\View;
use sfdp\service\Control as sfdpapi;
use tpflow\service\method\Jwt as workflow;
use sfdp\Api;

class Sfdp
{
    static function init($sid,$data){
        $list =  (new sfdpapi($sid))::curd('GetData',$sid,$data);
        return ['sid'=>$sid,'title'=>$list['list']['title'],'table'=>$list['list']['field']['db_name'],'btn'=>$list['list']['field']['btn']];
    }

    static function sfdpView($sid,$id){
        $sub = [];
        $view = (new sfdpapi($sid))->curd('view', $sid, $id);
        $view_cont = json_decode($view['info'],true);
        $ret_event = GyEvent('CurdEvent',['name_db'=>$view_cont['name_db'],'act'=>'view_after','data'=>$view,'id'=>$sid]);
        if($ret_event['code'] == 1 && is_array($ret_event['msg'])){
            $view = $ret_event['msg'];
        }

        if(!empty($view['i']['f']['sublists'])){
            $title = [];
            $key = [];
            $row =[];
            foreach ($view['i']['f']['sublists'] as $kk=>$vv){
                if(!empty($vv)){
                    $key[$kk]= array_keys($vv[0]);
                }
            }

            $tr = '';$th = '';
            foreach ($view['i']['f']['sublists'] as $kk=>$vv){
                if(!empty($vv)){
                foreach($vv as $kkk=>$vvv){
                    $title = array_column($vvv, 'tpfd_name');
                    if($kkk==0){
                        foreach($title as $t1){
                            $th .='<td>'.($t1 ?? '').'</td>';
                        }
                    }
                    $td = [];
                    $keys = $key[$kk];
                    $tr .= '<tr>';
                    foreach($keys as $k3=>$v3){
                        if($v3<>'id'){
                            $tr .='<td>'.($vvv[$v3]['value'] ?? '').'</td>';
                            $td[$vvv[$v3]['tpfd_db']] = $vvv[$v3]['value'];
                        }
                    }
                    $tr.= '</tr>';
                    $trs[$kkk] = $td;
                    unset($td);
                }
                $sub[$kk] = ['t'=>$title,'d'=>$trs,'tr'=>'<table style="width:100%;border-collapse: collapse;border: 0px;"><tr>'.$th.'</tr>'.$tr.'</tr></table>'];
                unset($title);$tr = '';$th = '';
                }else{
                    $sub[$kk] = ['t'=>'','d'=>'','tr'=>'<table style="width:100%;border-collapse: collapse;border: 0px;"><tr><td>无</td></tr></table>'];
                }
            }
        }
        if(in_array('WorkFlow', $view_cont['tpfd_btn'])){
            $wf_log = Tpflow::logData($view_cont['name_db'],$id);
            foreach($wf_log as $k=>$v){
                $wf_log[$k]['name'] = Db::name('soft_user')->where('id',$v['uid'])->value('realname');
            }
            $wf_log_html = '<table style="width:100%;border-collapse: collapse;border: 0px;"> <tr style="height:25pt;text-align:center;background: aliceblue;"><td style="width:20%;">审批时间</td><td style="width:45%;">审批意见</td><td style="width:15%;">审批动作</td><td style="width:20%;">审批人</td></tr>';
            $wf_log_html_tr = '';
            foreach($wf_log as $k=>$v){
                $wf_log_html_tr .='<tr style="height:25pt;text-align:center"><td>'.date('Y-m-d H:i:s',$v['dateline']).'</td><td >'.$v['content'].' </td><td >'.$v['btn'].'</td><td>'.$v['user'].'</td></tr>';
            }
            $wf_log_html .=$wf_log_html_tr.'</table>';
            $wf = ['d'=>$wf_log,'tr'=>$wf_log_html];
        }else{
            $wf = ['d'=>[],'tr'=>''];
        }
        return ['info' => $view['info'],'config' => $view['config'],'v' => $view['i']['f']['z'],'sub'=>$sub,'wf'=>$wf, 'sid' => $sid, 'id' => $id,'files'=>$view['i']['files']];
    }

    static function Data($init,$page=1,$field='',$title=''){
        $map = [];
        $offset = ($page-1)*10;
        $list = Db::name($init['table'])->where($map)->limit($offset,10)->order('id desc')->field($field)->select()->toArray();
        $stv = [
            -1=>'退回修改',0=>'保存中',1=>'流程中',2=>'审核通过'
        ];
        foreach($list as $k=>$v){
            $list[$k]['status_val'] =$stv[$v['status']];
            $list[$k]['table'] =$init['table'];
            $list[$k]['title'] =Db::name('soft_user')->where('id',$v['uid'])->value('username');
            $list[$k]['content'] =View::display($title,['v'=>$v]);
        }
        return ['data'=>$list,'placeholder'=>'输入关键字~'];
    }
    /**
     * 数据添加
     * @param $subdata
     * @param $id
     * @param string $type
     */
    static function Sfdp_save($data,$softId='',$s_sys_check='',$platform=0){
        $g_is_wf = 0;
        if(isset($data['g_is_wf'])){
            $g_is_wf = 1;
            unset($data['g_is_wf']);
        }
        /*添加前执行的动作*/
        $ret_event = GyEvent('CurdEvent',['name_db'=>$data['name_db'],'act'=>'add_before','data'=>$data]);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = implode(",", $v);
            }
        }
        $data['uid'] = $softId;
        $data['create_time'] = time();
        if($data['@saas_id']==0){
            $data['saas_id'] = session('softSaasId');
        }
        if($s_sys_check==1){
            $data['create_ip'] = request()->ip();
            $data['create_os'] = \Agent::getOs() .'　&　'.\Agent::getBroswer();
        }
        $table = $data['name_db'];
        $subdata = json_decode($data['@subdata'], true);
        unset($data['name_db'],$data['tpfd_check'],$data['@subdata'],$data['@saas_id']);
        Db::startTrans();
        try{
            $did = Db::name($table)->insertGetId($data);//添加主表
            self::sub_action($subdata,$did);//处理子表事务
            if($platform==0){
                self::oplog($table,$did);//添加日志
            }else{
                self::oplog($table,$did,'','',$softId);//添加日志
            }

            Db::commit();
            /*添加后执行的动作*/
            $ret_event = GyEvent('CurdEvent',['name_db'=>$table,'act'=>'add_after','data'=>$data,'id'=>$did]);
            if($ret_event['code'] == 1){
                return json($ret_event);
            }
            /*发起工作流操作*/
            if($g_is_wf==1){
                self::star_flow($table,$did);
            }
            return msg_return('添加成功！',0,$did);
        }catch(\Exception $e){
            // 回滚事务
            Db::rollback();
            return msg_return('添加失败'.$e->getMessage(), 1);
        }
    }

    static function sfdp_desic_all($data){
        $List =[];
        $fields =[];
        $desic_data = json_decode($data['data'],true);
        foreach($desic_data['list'] as $k) {
            $array = array_values($k['data']);
            for ($x = 1; $x <= $k['type']; $x++) {
                $td_type = $array[$x - 1]['td_type'] ?? '';
                if ($td_type != 'group') {
                    $title = $array[$x - 1]['tpfd_name'] ?? '';
                    $type = $array[$x - 1]['td_type'] ?? '';
                    $source = [];
                    if ($type == 'dropdown') {
                        $tpfd_data = $array[$x - 1]['tpfd_data'] ?? [];
                        foreach ($tpfd_data as $k2 => $v2) {
                            $source[] = [
                                'id' => $k2, 'name' => $v2
                            ];
                        }
                    }
                    $options = '';
                    if ($type == 'number') {
                        $type = 'numeric';
                    }
                    if ($type == 'date') {
                        $type = 'calendar';
                        $default_data = ['yyyy', 'MM-dd', 'yyyy-MM-dd', 'yyyyMMdd', 'yyyy-MM', 'yyyy-MM-dd HH:mm:ss'];
                        $options = ['format' => $default_data[$array[$x - 1]['xx_type']]];
                    }
                    if ($title != '') {
                        $List[] = [
                            'title' => $title,
                            'value' => $array[$x - 1]['tpfd_db'] ?? '',
                            'type' => $type,
                            'source' => $source ?? '',
                            'width' => $array[$x - 1]['tpfd_width'] ?? '',
                            'options' => $options
                        ];
                        $fieldmast[] = $array[$x - 1]['tpfd_must'] ?? 1;
                        $fields[] = $array[$x - 1]['tpfd_db'] ?? '';
                    }
                }
            }
        }
        return ['list'=>$List,'fields'=>$fields,'fieldmast'=>$fieldmast,'db'=>$desic_data['name_db']];
    }
    /**
     * 数据添加
     * @param $subdata
     * @param $id
     * @param string $type
     */
    static function Sfdp_edit($data,$id,$wf=0,$softId='',$platform=0){
        $g_is_wf = 0;
        if(isset($data['g_is_wf'])){
            $g_is_wf = 1;
            unset($data['g_is_wf']);
        }
        /*修改前执行的动作*/
        $ret_event = GyEvent('CurdEvent',['name_db'=>$data['name_db'],'act'=>'edit_before','id'=>$id,'data'=>$data,'post'=>'post']);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = implode(",", $v);
            }
        }
        /*流程中的编辑*/
        if($wf==0){
            $data['status'] = 0;
        }else{
            $data['status'] = 1;
        }
        $data['update_time'] = time();
        $table = $data['name_db'];
        $subdata = json_decode($data['@subdata'], true);
        unset($data['name_db'],$data['tpfd_check'],$data['@subdata'],$data['@saas_id']);
        Db::startTrans();
        try {
            Db::name($table)->where('id', $id)->update($data);
            self::sub_action($subdata,$id,'edit');//处理子表事务
            if($platform==0){
                self::oplog($table,$id,1);
            }else{
                self::oplog($table,$id,1,'',$softId);//添加日志
            }
            Db::commit();
            /*修改后执行的动作*/
            $ret_event = GyEvent('CurdEvent',['name_db'=>$table,'act'=>'edit_after','id'=>$id,'data'=>$data]);
            if($ret_event['code'] == 1){
                return json($ret_event);
            }
            /*发起工作流操作*/
            if($g_is_wf==1 && $wf==0){
                self::star_flow($table,$id);
            }
            return msg_return('编辑成功！',0,$id);
        }catch(\Exception $e) {
            // 回滚事务
            Db::rollback();
            return msg_return('修改失败:'.$e->getMessage(), 1);
        }
    }
    /**
     * 子表处理方法
     * @param $subdata
     * @param $id
     * @param string $type
     */
    static function sub_action($subdata,$id,$type='add'){
        if (is_array($subdata) && count($subdata) > 0) {
            foreach ($subdata as $kk => $vv) {
                if($type != 'add'){
                    $has_id = Db::name($kk)->where('d_id',$id)->find();
                    if($has_id){
                        Db::name($kk)->where('d_id', $id)->delete();
                    }
                }
            }
            $subDatas = self::sub_trans($subdata);

            foreach ($subDatas as $k => $v) {
                /*删除子表数据*/
                if($type != 'add'){
                    $ids = array_filter(array_column($v,'id'));//提交的ids
                    for($i=0;$i<count($ids);$i++)
                    {
                        $ids[$i] = intval($ids[$i]);
                    }
                    $old_ids = Db::name($k)->where('d_id',$id)->column('id');
                    $diff = array_diff($old_ids,$ids);
                    foreach($diff as $vv){
                        Db::name($k)->where('id', $vv)->delete();
                    }
                }
                /*更新或添加子表数据*/
                foreach ($v as $vv) {
                    $vv['uid'] = session('softId');
                    $vv['d_id'] = $id;
                    if(isset($vv['id']) && $vv['id'] <> ''){
                        $vv['update_time'] = time();
                        $ret = Db::name($k)->where('id',$vv['id'])->update($vv);
                    }else{
                        $vv['create_time'] = time();
                        $ret = Db::name($k)->insertGetId($vv);
                    }
                    if (!$ret) {
                        return false;
                    }
                }
            }
        }else{
            return true;
        }
    }

    /**
     * 子表数据转换
     * @param $subdata
     * @return array
     */
    static function sub_trans($subdata){
        $subDatas = [];
        foreach ($subdata as $k => $v) {
            if(empty($v)){
                continue;//
            }
            $keys = array_keys($v);//取出所有
            $num = count($v[$keys[0]]);//计算下有几列数据
            //数组转换，将值合并
            unset($new);
            for ($x = 0; $x < $num; $x++) {
                $new[$x] = [];
                foreach ($keys as $key) {
                    //相同顺序的值Push到同个数组
                    $new_key = str_replace("[]", "", $key);
                    array_push($new[$x], [$new_key => $v[$key][$x]]);
                }
            }
            //将值对应转成二维数组
            foreach ($new as $vv) {
                //二维数组转一维数组，并赋值给对应的数据表
                $subDatas[$k][] = array_reduce($vv, 'array_merge', array());
            }
        }
        return $subDatas;
    }
    static function status($id,$table,$status,$uid=0){
        /*审核前后执行的动作*/
        $ret_event = GyEvent('CurdEvent',['act'=>'check_start','id'=>$id,'status'=>$status,'name_db'=>$table]);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        $data = [
            'status' => $status,
            'update_time' => time(),
            'id' => $id
        ];
        $ret = Db::name($table)->update($data);
        if ($ret) {
            /*审核后后执行的动作*/
            $ret_event = GyEvent('CurdEvent',['act'=>'check_fun','id'=>$id,'status'=>$status,'name_db'=>$table]);
            if($ret_event['code'] == 1){
                return json($ret_event);
            }
            $stv = [-1 => '退回修改', 0 => '保存中', 1 => '流程中', 2 => '审核通过'];
            self::oplog($table,$id,3,'变更权限为:'.($stv[$status] ?? 'Err,系统错误'),$uid);
            return true;
        } else {
            return false;
        }
    }
    /**
     * 日志处理
     * @param $bill_table
     * @param $bill_id
     * @param int $type
     * @param string $con
     */
    static function oplog($bill_table,$bill_id,$type=0,$con='',$uid=0){
        if(stripos($_SERVER['HTTP_USER_AGENT'],"android")!=false||stripos($_SERVER['HTTP_USER_AGENT'],"ios")!=false||stripos($_SERVER['HTTP_USER_AGENT'],"wp")!=false){
            $op_platform = 1;
        }else{
            $op_platform = 0;
        }
        if($uid==0){
            $op_uid = session('softId');
            $op_user = session('sfotUserName');
        }else{
            $op_uid = $uid;
            $op_user = Db::name('soft_user')->where('id',$uid)->value('username') ?? '';
        }
        $op_type = ['新增','修改','删除','变更'];
        $oplog = [
            'bill_table'=>$bill_table,
            'bill_id'=>$bill_id,
            'op_uid'=>$op_uid,
            'op_user'=>$op_user,
            'op_time'=>date('Y-m-d H:i:s'),
            'op_type'=>$op_type[$type] ?? $type,
            'op_ip'=>request()->ip(),
            'op_os'=>\Agent::getOs() .'　&　'.\Agent::getBroswer(),
            'op_con'=>$con,
            'op_platform'=>$op_platform
        ];
        $ret = Db::name('soft_oplog')->insertGetId($oplog);
        if(!$ret){
            return msg_return('记录日志信息失败', 1);
        }
    }
    static function btnArray($btnArray, $sid,$style){
        $wf_access = g_cache('wf_qs');
        $url = url('add', ['sid' => $sid]);
        $wh = '{w:"98%",h:"98%"}';
        if(isset($style['tpfd_open']) && count(explode(',',$style['tpfd_open']))>1){
            $wh_data = explode(',',$style['tpfd_open']);
            $wh = '{w:"'.$wh_data[0].'%",h:"'.$wh_data[1].'%"}';
        }
        $btns = '';
        if (g_btn_access($sid,'add') && (in_array('add', $btnArray))) {

            $btns .= '<a class="layui-btn layui-btn-sm addbtn layui-btn-primary" onclick=sfdp.openpage("新增","' . $url . '",'.$wh.') >新增</a> ';
        }
        if (g_btn_access($sid,'edit') && (in_array('Edit', $btnArray))) {
            $btns .= '<a onClick=edit(' . $sid . ','.$wh.') class="layui-btn layui-btn-sm  editbtn">修改</a> ';
        }
        if (g_btn_access($sid,'del') && in_array('Del', $btnArray)) {
            $btns .= ' <a onClick="del(' . $sid . ')"	class="layui-btn layui-btn-sm layui-btn-danger delbtn">删除</a> ';
        }

        $wf = '';
        if ((!in_array('WorkFlow', $btnArray)) && (in_array('Status', $btnArray))) {
            if (g_btn_access($sid,'Status')) {
                $wf = '	<a class="layui-btn layui-btn-sm layui-bg-cyan  qsbtn" onclick=status_ok('.$sid.',2,"核准")> 核准</a> <a class="layui-btn layui-btn-sm layui-bg-gray" onclick=status_ok(' . $sid . ',0,"去审")>去审</a>';
            }
        }
        if (g_btn_access($sid,'workflow') && in_array('WorkFlow', $btnArray)) {
            $wf = '	<a class="layui-btn layui-btn-sm layui-bg-green zzbtn" onclick="end_flow()">终止</a> ';
            if ((in_array(session('softId'), explode(',',$wf_access)))) {
                $wf .= '<a class="layui-btn layui-btn-sm  layui-bg-orange" onclick="cancel_flow()">去审</a>';
            }
        }
        $Import = '';
        if (g_btn_access($sid,'import') && in_array('Import', $btnArray)) {
            $url = url('import', ['sid' => $sid]);
            $Import = ' <a onclick=sfdp.openfullpage("快速导入","' . $url . '")	class="layui-btn layui-btn-sm layui-bg-gray impbtn">导入</a> ';
        }
        return $btns . $wf .$Import;
    }
    /**
     * 将函数方法转化为树方法
     * @param $fun_name
     */
    static function tree_data($fun_name){
        if($fun_name=='sys_role'){
            $ret = Db::name('softRole')->where('id','>',1)->field('id,name as title,pid')->select()->toArray();
            return g_generateTree($ret);
        }
        $Source = app('app\gadmin\controller\Source');
        $data =  $Source->api($fun_name);
        $ret = json_decode($data->getContent(),true);
        if($ret['code'] == 1){
            echo '<h2>系统级别错误('.$fun_name.')</h2>';exit;
        }
        if($ret['code'] == -1){
            echo $ret['msg'];exit;
        }else{
            return g_generateTree($ret['data']);
        }
    }
    /**
     * 将函数方法转化为树方法
     * @param $fun_name
     */
    static function tab_data($fun_name){
        $Source = app('app\gadmin\controller\Source');
        $data =  $Source->api($fun_name);
        $ret = json_decode($data->getContent(),true);
        if($ret['code'] == 1){
            echo '<h2>系统级别错误('.$fun_name.')</h2>';exit;
        }
        if($ret['code'] == -1){
            echo $ret['msg'];exit;
        }else{
            return $ret['data'];
        }
    }
    static function star_flow($table,$id){
        $wf_id = Db::name('wf_flow')->where('type',$table)->where('status',0)->value('id');
        if(!$wf_id){
            return msg_return('发起失败,未能找到流程引擎！！',1);
        }
        $wf = (new workflow())->WfCenter('start','','',['wf_fid'=>$id,'wf_id'=>$wf_id,'check_con'=>'保存后-发起']);
        if(!isset($wf)){
            return msg_return('Success,未能找到流程引擎！！',1);
        }
        if($wf['code']=='-1'){
            return msg_return($wf['msg'],1);
        }
    }
    static function hasWorkflow($data){
        $has = in_array('WorkFlow', json_decode($data['data'],true)['tpfd_btn']);
        if($has){
            return 1;
        }else{
            return 2;
        }
    }
    /**
     * 获取已经激活的业务信息
     */
    static function getActiveData(){
        return Db::name('sfdp_design')->where('s_design',2)->field('id,CONCAT(s_bill,"-",s_title,"-",s_db) as name')->select()->toArray();
    }
    /**
     * 获取设计器详细信息
     */
    static function getSfdpData($id){
        return Db::name('sfdp_design')->find($id);
    }

}