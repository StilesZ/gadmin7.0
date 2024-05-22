<?php
/**
 *+------------------
 * Gadmin 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Config;
use sfdp\Api;
use think\facade\Db;
use think\facade\View;
use app\common\server\App as AppServer;

class App extends Base
{
    /**
     * 列表方法
     */
    public function index($page = 1, $limit = 10, $draw = 1, $map = [])
    {
        if ($this->request->isPost()) {
            if (input("table")) $map[] = ['table', 'like', '%' . input('table') . '%'];
            if (input("title")) $map[] = ['title', 'like', '%' . input('title') . '%'];
            $List = commonListData('soft_app', $map, $page, $limit, 'id,"title",add_name,status,app_id,add_time');
            $json = $List['list'];
            $status = [0 => '保存中', 1 => '退回', 2 => '审核通过'];
            $jsondata = [];
            foreach ($json as $k => $v) {
                $json[$k]['title'] = Db::name('soft_app_config')->where('id', $v['app_id'])->value('title');
                unset($json[$k]['app_id']);
                $json[$k]['status'] = $status[$v['status']] ?? 'ERR';
                $jsondata[$k] = array_values($json[$k]);
            }
            return json(["draw" => $draw, "recordsTotal" => $List['count'], "recordsFiltered" => $List['count'], 'data' => $jsondata]);
        }
        return view();
    }

    /**
     * 添加
     */
    public function add_view($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['update_time'] = time();
            $ret = Db::name('soft_app_config')->where('id',$id)->update($data);
            if ($ret) {
                return msg_return('修改成功！');
            } else {
                return msg_return('修改失败！', 1);
            }
        }
        $info = Db::name('soft_app_config')->find($id);
        if($info['typeid']>0){
         $field = $this->getfield($info['table']);
        }
        return view('add_view', ['fied'=>$field ?? [],'con' => $info['yw_content'], 'info' => $info]);
    }
    
    /**
     * 头部设计
     */
    public function base($map = [])
    {
        if ($this->request->isPost()) {
            if (input("table")) $map[] = ['table', 'like', '%' . input('table') . '%'];
            if (input("title")) $map[] = ['title', 'like', '%' . input('title') . '%'];
            if (input("sid")) $map[] = ['typeid', '=', input('sid') ];
            $offset = (input('page') - 1) * input('limit');
            $list = Db::name('soft_app_config')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $status = [0 => '保存中', 1 => '退回', 2 => '审核通过'];
            foreach ($list as $k => $v) {
                if($v['sid']==0){
                    $list[$k]['sid'] = '系统表单';
                }else{
                    $list[$k]['sid'] = Db::name('sfdp_design')->where('id', $v['sid'])->value('s_title');
                }

                $list[$k]['type'] = Db::name('soft_app_type')->where('id', $v['typeid'])->value('title') ?? '系统表单';
                $list[$k]['stv'] = g_common_status($v['status']);
            }
            $count = Db::name('soft_app_config')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        $tree= Db::name('soft_app_type')->field('id,title as name,id as pid,title')->select()->toArray();
        return view('base',['tree'=>json_encode($tree)]);
    }


    /**
     * 状态核准
     */
    public function status_report($id, $status)
    {
        $data = [
            'status' => $status,
            'id' => $id
        ];
        $ret = Db::name('soft_app')->update($data);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
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
        $ret = Db::name('soft_app_config')->update($data);
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
        $ret = Db::name('soft_app_config')->delete($id);
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
            $data['add_name'] = session('sfotUserName');
            $data['add_time'] = date('Y-m-d H:i:s');
            $ret = Db::name('soft_app_config')->insertGetId($data);
            if ($ret) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加失败', 1);
            }
        }
        return view('add', ['tag' => AppServer::tag(),'yw'=>\app\common\server\Sfdp::getActiveData()]);
    }

    /**
     * 修改头部
     *
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $ret = Db::name('soft_app_config')->update($data);
            if ($ret) {
                return msg_return('修改成功！');
            } else {
                return msg_return('修改失败', 1);
            }
        }
        $info = Db::name('soft_app_config')->find($id);
        return view('add', ['info' => $info, 'tag' => AppServer::tag(),'yw'=>\app\common\server\Sfdp::getActiveData()]);
    }

    /**
     * 获取数据表名
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_table($id)
    {
        return json(['code' => 0, 'msg' => \app\common\server\Sfdp::getSfdpData($id)]);
    }

    /**
     * 获取字段内容
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getfield($name)
    {
        $table = config('database.connections.mysql.prefix') . $name;
        return  Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM information_schema.columns WHERE  TABLE_NAME='{$table}'");
    }
    public function buildapp($sid){
		$config = (new Api($sid))->sfdpCurd('add', $sid);//如果没有发行版本，会直接输出：
        if($config['config']['s_type']==1){
            return json(['code' => 1, 'msg' => '编辑器表单，暂时不支持哦！']);
        }
		$data = json_decode($config['data'],true);
		$app = Db::name('soft_app_config')->where('sid',$sid)->find();
        /*界面Html还原*/
        $table ='<p style="text-align: center;"><span style="font-size: 24px;">'.$data['name'].'</span></p><table style="width: 100%" class="table table-bordered" data-sort="sortDisabled">';
        $max_ros = max(array_column($data['list'], 'type'));
        foreach($data['list'] as $k){
            $td ='';
            $array = array_values($k['data']);
            for ($x=1; $x<=$k['type']; $x++) {
                $td_type = $array[$x-1]['td_type'] ?? '';
                if($td_type!='group'){
                    $title = $array[$x-1]['tpfd_name'] ?? '';
                    if(isset($array[$x-1]['tpfd_db'])){
                        $value = '{$data.'.($array[$x-1]['tpfd_db'] ?? '').'}';
                    }else{
                        $value = '';
                    }
                    if($x == $k['type'] & $x != $max_ros){
                        $colspan = $max_ros;
                        $td .= '<td valign="middle" style="word-break: break-all; border-color: rgb(221, 221, 221);" width="84" align="center">'.$title.'</td><td  colspan="'.(intval($colspan) - intval($k['type']) + 2).'">'.$value.'</td>';
                    }else{
                        $td .= '<td valign="middle" style="word-break: break-all; border-color: rgb(221, 221, 221);" width="84" align="center">'.$title.'</td><td class=>'.$value.'</td>';
                    }
                }
            }
            $table .= '<tr>'.$td.'</tr>';
        }
		if(!$app){
			$app_config['title'] = 'APP-'.$data['name'];
			$app_config['table'] = $data['name_db'];
            $app_config['sid'] = $sid;
            $app_config['content'] = '一键构建APP';
            $app_config['typeid'] = 1;
            $app_config['icon'] = 'share';
			$app_config['status'] = 0;
			$app_config['add_name'] = session('sfotUserName');
			$app_config['add_time'] = date('Y-m-d H:i:s');
            $app_config['yw_content'] = $table;
			$app_config_id = Db::name('soft_app_config')->insertGetId($app_config);
			if(!$app_config_id){
				return json(['code' => 1, 'msg' => '设置APP配置失败！']);
			}
		}else{
            Db::name('soft_app')->update(['id'=>$app['id'],'con'=>$table]);
        }
		return json(['code' => 0, 'msg' => '操作成功！']);

    }
    public function app_tag($id=''){
        if ($this->request->isPost()) {
            return AppServer::saveTag(session('softId'),input('post.'));
        }
        return view('tag',['info'=>AppServer::find($id,'soft_app_type'),'type'=>AppServer::tag(session('softId'))]);
    }

    public function config(){
        if ($this->request->isPost()) {
            $config = input('post.');
            foreach ($config as $k => $c) {
                $has = Db::name('soft_config')->where('only_tag',$k)->find();
                if($has){
                    Db::name('soft_config')->where('id',$has['id'])->update(['update_time'=>time(),'only_tag'=>$k,'value'=>$c]);
                }else{
                    $oldconfigname = ['app_name'=>'APP名称','app_ver'=>'APP版本号','app_ad'=>'APP滚动广告'];
                    Db::name('soft_config')->insertGetId(['value'=>$c,'only_tag'=>$k,'name'=>$oldconfigname[$k] ?? '','update_time'=>time(),'type'=>0]);
                }
            }
            Config::Init(1);
            $this->success('更新成功！');
        }
        $sys = g_cache('g_soft_cache_config');
        return view('',['sys' => $sys]);
    }
}
