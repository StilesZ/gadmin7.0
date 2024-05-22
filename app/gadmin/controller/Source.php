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

use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use think\response\Json;
use app\common\server\Sfdp as Sapi;

class Source extends Base
{
    /**
     * 列表信息
     * @param array $map
     * @return Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index($map = [])
    {
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("username")) $map[] = ['title|fun', 'like', '%'.input('username').'%'];
            if (input("table")) $map[] = ['table', '=', input('table')];
            $list = Db::name('soft_source')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $count = Db::name('soft_source')->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        $data = Db::name('soft_source')->group('table')->column('table');
        $tableInfo = Db::connect('mysql')->query("show table status");
        $arr = [];
            $arr[]= ['title'=>'<b style="font-size: initial;">模型表数据中心</b>','id'=>1,'pid'=>0];
        foreach ($tableInfo as $k => $v){
            if(in_array($v['Name'],$data)){
                $arr[]= ['title'=>str_replace('[work]','',$v['Comment']).'['.$v['Name'].']','id'=>$v['Name'],'pid'=>1];
            }
        }
        return view('',['tree'=>json_encode(g_generateTree($arr))]);
    }

    /**
     * 数据添加操作
     * @return Json|\think\response\View
     */
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();

            if(Db::name('soft_source')->where('fun',$data['fun'])->find()){
                return msg_return('函数名重复');
            }
            $data['add_name'] = session('sfotUserName');
            $data['add_time'] = date('Y-m-d H:i:s');
            $ret = Db::name('soft_source')->insertGetId($data);
            if ($ret) {
                $this->success('add',url('edit',['id'=>$ret]));
            } else {
                return msg_return('写入数据库失败！');
            }
        }
        $connections = Config::get('database.connections');
        foreach ($connections as $k => $v){
            $conn[] = $k;
        }
        View::assign('connections',$conn);
        return view();
    }

    /**
     * 数据修改
     * @param $id
     * @return Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['add_time'] =date('Y-m-d H:i:s');
            $ret = Db::name('soft_source')->update($data);
            if ($ret) {
                return msg_return('修改成功！', 1);
            } else {
                return msg_return($ret['data']);
            }
        }
        $data = Db::name('soft_source')->find($id);
        if($data['table']!='请选择数据表'){
            $data['fieldList'] = $this->FieldList($data['conn'],$data['table']);
        }
        $connections = Config::get('database.connections');
        foreach ($connections as $k => $v){
            $conn[] = $k;
        }
        $join = Db::name('soft_source_join')->where('sid',$id)->select();
        View::assign('join',$join);
        View::assign('connections',$conn);
        View::assign('row',$data);
        return view('add');
    }

    /**
     * 关联字表
     * @param $id
     * @return Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function join($id){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['add_name'] = session('sfotUserName');
            $data['add_time'] = date('Y-m-d H:i:s');
            $ret = Db::name('soft_source_join')->insertGetId($data);
            if ($ret) {
                return msg_return('登记成功！');
            } else {
                return msg_return($ret, 1);
            }
        }
        $table = $this->getTableList();
        $table_data = json_decode($table->getContent(),true);
        $data = Db::name('soft_source')->find($id);
        $data['fieldList'] = $this->FieldList($data['conn'],$data['table']);
        View::assign('table',$data['table']);
        View::assign('table_data',$table_data['data']);
        View::assign('data',$data);
        return view();
    }

    /**
     * 删除关联的表
     * @param $id
     * @return Json
     * @throws \think\db\exception\DbException
     */
    public function deljoin($id){
        $ret = Db::name('soft_source_join')->delete($id);
        if($ret){
            return msg_return('删除成功！');
        }else{
            return msg_return('删除失败',1);
        }
    }

    /**
     * 删除元素及关联表
     * @param $id
     * @return Json
     * @throws \think\db\exception\DbException
     */
    public function del($id){
        $ret = Db::name('soft_source')->delete($id);
        if($ret){
            Db::name('soft_source_join')->where('sid',$id)->delete();
            return msg_return('删除成功！');
        }else{
            return msg_return('删除失败',1);
        }
    }


    /**
     * created by slyue(www.yueshaoliang.com)
     * 2021/2/15 18:57
     * @param string $connection
     * @param string $table
     * @return array
     */
    private function FieldList(string $connection,string $table)
    {
        //mysql数据库引擎
        if(config('database.connections.'.$connection.'.type')=='mysql'){
            $tableInfo = Db::connect($connection)->query("show full columns from ".$table);
        }
        //sqlServer数据库引擎
        if(config('database.connections.'.$connection.'.type')=='Sqlsrv'){
            $tableInfo = Db::connect($connection)->query("select a1.name as Field,b.value as Comment from sysobjects a left join  sys.columns a1 on a.id = a1.object_id left join sys.extended_properties b on b.major_id = a.id and b.minor_id = a1.column_id
where a.name='".$table."'");
        }
        $fieldList = [];
        foreach ($tableInfo as $k => $v){
            $field = [
                "value"=>$v['Field'],
                'title'=>$v['Comment'],
            ];
            $fieldList[] = $field;
        }
        return $fieldList;
    }

    /**
     * 获取table信息
     * @return Json
     */
    public function getTableList(): Json
    {
        $connection = Request::param('connection','mysql');
        //mysql数据库引擎
        if(config('database.connections.'.$connection.'.type')=='mysql'){
            $tableInfo = Db::connect($connection)->query("show table status");
        }
        //sqlServer数据库引擎
        if(config('database.connections.'.$connection.'.type')=='Sqlsrv'){
            $tableInfo = Db::connect($connection)->query("SELECT TABLE_NAME as Name FROM INFORMATION_SCHEMA.TABLES  where table_schema = 'dbo'");
        }
        $arr = [];
        foreach ($tableInfo as $k => $v){
            $arr[] = $v['Name'];
        }
        return json(['code'=>200,'data'=>$arr]);
    }
    /**
     * 获取字段信息
     * @return Json
     */
    public function getFieldList(): Json
    {
        $connection = Request::param('connection','');
        $table = Request::param('table','');
        $fieldList = $this->FieldList($connection,$table);
        return json(['code'=>200,'data'=>$fieldList]);
    }
    public function run($id){
        $sourec = Db::name('soft_source')->find($id);
        if(strpos($sourec['where'],':') !== false ){
            $step =json_encode(['msg'=>'您需要先填写传递参数','where'=>$sourec['where']]);
            $where =1;
        }else{
            $step =($this->apiId($id)->getContent());
            $where =2;
        }
        View::assign('where',$where);
        View::assign('info',$sourec);
        View::assign('step',$step);
        return view();
    }
    /**
     * @param $id
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function apiId($id,$map=[]) : Json
    {
        $sourec = Db::name('soft_source')->find($id);
        if(!$sourec){
            return  msg_return('找不到函数名信息',1);
        }
        if(count($map) > 0){
            $postData = $map;
        }else{
            $postData = $this->request->post();
        }
        $page = $this->request->param('page') ?? 1;            //业务的页面,可不填写
        $limit = $this->request->param('limit') ?? 20;            //每页显示数量
        $data = $this->getData($sourec['id'],$page,$limit,$postData);
        return json($data);
    }
    /**
     * Api 函数名 接口调用方法
     * @param $fun
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function api($fun,$act='',$open=0,$userinfo=[]) : Json
    {
        $sourec = Db::name('soft_source')->where('fun',$fun)->find();
        if(!$sourec){
            return  msg_return('函数名有误',1);
        }
        if($open==1  && $sourec['open']==0){
            return  msg_return('对不起，元素未开启！！',1);
        }
        $postData = $this->request->post();
        $page = $this->request->param('page') ?? 1;            //业务的页面,可不填写
        $limit = $this->request->param('limit') ?? 20;            //每页显示数量
        $data = $this->getData($sourec['id'],$page,$limit,$postData,$act,$userinfo);
        return json($data);
    }
    /**
     * 函数调用请求方法
     * @return Array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getData($sourceId,$page,$limit,$postData,$act='',$userinfo=[]) : Array
    {
        $sourec = Db::name('soft_source')->find($sourceId);    //业务信息
        $data = Db::connect($sourec['conn'])->table($sourec['table']);
        if(!empty($userinfo)){
            $softId = $userinfo['uid'];
            $sfotRoleId= $userinfo['role'];
        }else{
            $softId = session('softId');
            $sfotRoleId= session('sfotRoleId');
        }
        if($sourec['where'] != ''){
            if(strpos($sourec['where'],':') !== false ){
                $map = $postData;
                if(count($postData)>0){
                    $where = $sourec['where'];
                    $where = str_replace("{userid}",$softId,$where);
                    $where = str_replace("{roleid}",$sfotRoleId,$where);
                    $data = $data->whereRaw($where, $map);
                }
            }else{
                $map =[];
                $where = $sourec['where'];
                $where = str_replace("{userid}",$softId,$where);
                $where = str_replace("{roleid}",$sfotRoleId,$where);
                if($act==''){
                    $data = $data->whereRaw($where, $map);
                }
            }
        }
        try{
            $join = Db::name('soft_source_join')->where('sid',$sourceId)->select()->toArray();
            foreach ($join as $k => $v){
                if($v['alias']==''){
                    $alias = $v['table'];
                }else{
                    $alias = $v['alias'];
                }
                if($v['link_alias']==''){
                    $link_alias = $sourec['table'];
                }else{
                    $link_alias = $v['link_alias'];
                }
                $data = $data->leftjoin($v['table'].' '.$alias,$alias.'.'.$v['link_id'].' = '.$link_alias.'.'.$v['link_mid']);
            }

            //custom 模式
            if($sourec['type']==5){
                $step = $this->request->action();
                if($step !='wfedit'){
                    if($act==''){//用户的查看，修改，忽略掉uid
                        $sourec['customsql'] = str_replace("{userid}",$softId,$sourec['customsql']);
                        $sourec['customsql'] = str_replace("{roleid}",$sfotRoleId,$sourec['customsql']);
                    }else{
                        $sourec['customsql'] = str_replace("={userid}",'>0',$sourec['customsql']);
                        $sourec['customsql'] = str_replace("={roleid}",'>0',$sourec['customsql']);
                    }
                }else{//工作流则忽略uid
                    $sourec['customsql'] = str_replace("={userid}",'>0',$sourec['customsql']);
                    $sourec['customsql'] = str_replace("={roleid}",'>0',$sourec['customsql']);
                }
                $data = Db::connect($sourec['conn']);
                $sql = $sourec['customsql'];
                $filter_res = array_filter($postData);
                if((strpos($sql,":")!==false && empty($postData)) || (!empty($postData) && empty($filter_res))){
                    $zhu_sql = strrchr($sql,'where');
                    $count = strpos($sql,"$zhu_sql");
                    $strlen = strlen($zhu_sql);
                    $zhu_sql = substr_replace($sql,"",$count,$strlen);
                    $group_order_sql = strrchr($sql,'group');
                    $sql = $zhu_sql.$group_order_sql;
                }else{
                    if(count($postData)>0){
                        if(isset($postData['page'])){
                            $postData = $postData['search'] ?? [];
                        }
                        foreach ($postData as $key=>$value){
                            $sql = str_replace("like :".$key,"like '%".$value."%'",$sql);
                            $sql = str_replace("like:".$key,"like '%".$value."%'",$sql);
                            $sql = str_replace(":".$key,"'".$value."'",$sql);
                            $sql = str_replace(": ".$key,"'".$value."'",$sql);
                        }
                    }

                }
                $data = $data->query($sql);
            }
            if(empty($sourec['order']) || $sourec['order']==''){
                $sourec['order'] = 'id desc';
            }
            //select 模式
            if($sourec['type']==0){
                $data = $data->field($sourec['field'])
                    //->limit($limit)
                    //->page($page)
                    ->order($sourec['order'])
                    ->select()
                    ->toArray();
            }
            //select group
            if($sourec['type']==1){
                $data = $data->field($sourec['field'])
                    //->limit($limit)
                    //->page($page)
                    ->order($sourec['order'])
                    ->group($sourec['group'])
                    ->select()
                    ->toArray();
            }
            //find
            if($sourec['type']==2){
                $data = $data->field($sourec['field'])->order($sourec['table'].'.'.'id desc')->find();
            }
            //update
            if($sourec['type']==3){
                $data = $data->update($postData);
            }
            //ins
            if($sourec['type']==4){
                $data = $data->insertGetId($postData);
            }
            if($data || empty($data)){
                if(env('APP_DEBUG')){
                    $last_sql = Db::getLastSql();
                    return ['code'=>0,'msg'=>'请求成功','data'=>$data,'sql'=>$last_sql];
                }else{
                    return ['code'=>0,'msg'=>'请求成功','data'=>$data];
                }

            }else{
                return ['code'=>1,'msg'=>'执行错误','data'=>$data,'sql'=>Db::getLastSql()];
            }
        }catch(\Exception $e){
            $info = [
                'type' =>'Err',
                'controller'       => 'Source',
                'uri'       => 'fun',
                'params'    => '',
                'ip'    => '',
                'runtime' => '',
                'memory'  => '',
                'sql' =>$e->getMessage(),
                'create_time'  => time()
            ];
            Db::connect('db_log')->name('err')->insert($info);
            return ['code'=>-1,'msg'=>'元数错误：'.$e->getMessage()];
        }
    }
}