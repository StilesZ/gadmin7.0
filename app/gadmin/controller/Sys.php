<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Config;
use app\common\server\Datarecycling;
use app\common\server\Initdata;
use app\common\server\Navigation;
use app\common\server\Widget;
use think\facade\Db;
use think\facade\Cache;
use app\common\server\Desk;
use app\common\server\Upgrade as Up;
use app\common\server\Schedule;
use Security;

class Sys extends Base
{
    /**
     * 基本配置
     */
    public function base()
    {
        $url = app()->getRootPath();
        if ($this->request->isPost()) {
            $config = input('post.');
                foreach ($config as $k => $c) {
                    $has = Db::name('soft_config')->where('only_tag',$k)->find();
                    if($has){
                        Db::name('soft_config')->where('id',$has['id'])->update(['update_time'=>time(),'only_tag'=>$k,'value'=>$c]);
                    }
                }
            Datarecycling::build($config['datarecycling']);
            Config::Init(1);
            $this->success('更新成功！');
        }
        /*新版本增加了缓存表*/
        Config::Build();
        $sys = g_cache('g_soft_cache_config');
        if(!isset($sys['wf_qs'])){
            $sys['wf_qs'] = '';
        }
        if(!isset($sys['sfdp_db'])){
            $sys['sfdp_db'] = '';
        }
        if(!isset($sys['sfdp_fix'])){
            $sys['sfdp_fix'] = '';
        }
        $user = Db::name('softUser')->where('status',1)->select();
        return view('base', ['sys' => $sys,'user'=>$user]);
    }

    /**
     * 消息配置
     */
    public function msg()
    {
        $url = app_path();
        if(!is_writable($url . '/config/msg.php')) {
            echo '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>(●◡●)</h1>对不起,目录文件写入权限不足，设置（/app/gadmin/config/msg.php 为读写权限）！</h2>';exit;
        }
        // 配置信息保存
        if ($this->request->isPost()) {
            $config = input('post.');
            $url = app_path();
            $config_new = array();
            $config_old = require $url . '/config/msg.php';
            if (is_array($config)) $config_new = array_merge($config_old, $config);
            arr2file($url . '/config/msg.php', $config_new);
            $this->success('更新成功！');
        }
        return view('msg', ['msg' => config('msg')]);
    }
    /**
     * 桌面系统
     */
    public function desk($map = [])
    {
        if ($this->request->isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("title")) $map[] = ['title', 'like', '%' . input('title') . '%'];
            $list = Db::name('soft_widget')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $type = [1 => '文本内容', 2 => '表格展示', 3 => '图表-柱状图', 4 => '图表-饼状图', 5 => '图表-折线图', 6 => '图表-面积图', 7 => '图表-玫瑰图', 8 => '图表-仪表盘', 9 => '图表-水波图'];
            foreach ($list as $k => $v) {
                $list[$k]['status_val'] = g_common_status($v['status']);
                $list[$k]['type_val'] = $v['type']==0 ? '桌面':'列表';
                $list[$k]['widgetType'] = $type[$v['widgetType']] ?? 'ERR';
            }
            $count = Db::name('soft_widget')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view();
    }

    /**
     * 桌面配置模块
     */
    public function deskConfig()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $w['widget'] = implode(',',$data['data']);
            $w['uid'] = session('softId');
            $w['utime'] = time();
            if (Db::name('soft_widget_user')->where('uid', session('softId'))->find()) {
                $ret = Db::name('soft_widget_user')->update($w);
            }else {
                $ret = Db::name('soft_widget_user')->insertGetId($w);
            }
            if ($ret) {
                return msg_return('操作成功');
            } else {
                return msg_return($ret['data'], 1);
            }
        }
        $WidgetUser = Db::name("soft_widget_user")->where('uid', session('softId'))->value('widget');
        $ids = explode(',',$WidgetUser ?? '');
        foreach($ids as $k=>$v){
            $has = Db::name('soft_widget')->find($v);
            if(!$has){
                unset($ids[$k]);
            }
        }
        $WidgetUser = implode(',',$ids);
        return view('deskConfig', ['widgets'=>Widget::data(),'selected' => explode(',',$WidgetUser), 'WidgetUser' => $WidgetUser]);
    }

    /**
     * 桌面配置模块
     */
    public function deskMain($id='')
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            if($data['data']=='fenpei'){
                return Widget::app($id);
            }
            if($data['data']=='save_layout'){
                $w['id'] = $id;
                $w['layout'] = $data['layout'];
                return Widget::saveTag($w);
            }
            if($data['data']=='save'){
                $w['id'] = $id;
                $w['ids'] = json_encode($data['ids'] ?? []);
                return Widget::saveTag($w);
            }
        }
        $WidgetUser = Widget::find($id);
        $con = [];
        $ids = [];
        if($WidgetUser){
            $content = json_decode($WidgetUser['ids'] ?? '',true);
            $layout = explode(';', $WidgetUser['layout'] ?? '');
            foreach ($layout as $k=>$v){
                $kv = explode(':',$v);
                $c = explode(',',$kv[0]);
                foreach($c as $kk=>$vv){
                    $c[$kk] = [$vv,($content[$k][$kk] ?? [])];
                    $ids[] = $content[$k][$kk] ?? [];
                }
                $con[]=[$kv[1],$c];
            }
        }
        $ids =  array_unique(array_merge(...$ids));
        foreach($ids as $k=>$v){
            $has = Db::name('soft_widget')->find($v);
            if(!$has){
                unset($ids[$k]);
            }
        }
        $WidgetUser = implode(',',$ids);
        return view('deskMain', ['widgets'=>Widget::data(),'type'=>Widget::tag(),'selected' => explode(',',$WidgetUser), 'WidgetUser' => $WidgetUser,'id'=>$WidgetUser['id'] ?? '','layout'=>$con]);
    }
    public function deskHome($id=''){
        if ($this->request->isPost()) {
            $data = input('post.');
            if($data['data']=='add'){
                Db::name('soft_widget_home')->insertGetId(['uptime'=>time(),'layout'=>'6,6:150','title'=>$data['title']]);
            }
            if($data['data']=='save'){
                Db::name('soft_widget_home')->where('id',$data['id'])->update(['uptime'=>time(),'content'=>json_encode($data['ids'] ?? [])]);
            }
            if($data['data']=='save_layout'){
                Db::name('soft_widget_home')->where('id',$data['id'])->update(['uptime'=>time(),'layout'=>$data['layout']]);
            }
            return msg_return('操作成功！');
        }
        $con = [];
        $ids = [];
        $WidgetUser = Db::name('soft_widget_home')->find($id);
        if($WidgetUser){
            $content = json_decode($WidgetUser['content'] ?? '',true);
            $layout = explode(';', $WidgetUser['layout'] ?? '');
            foreach ($layout as $k=>$v){
                $kv = explode(':',$v);
                $c = explode(',',$kv[0]);
                foreach($c as $kk=>$vv){
                    $c[$kk] = [$vv,($content[$k][$kk] ?? [])];
                    $ids[] = $content[$k][$kk] ?? [];
                }
                $con[]=[$kv[1],$c];
            }
        }
        $ids =  array_unique(array_merge(...$ids));
        foreach($ids as $k=>$v){
            $has = Db::name('soft_widget')->find($v);
            if(!$has){
                unset($ids[$k]);
            }
        }
        $Node = (new \sys\Node())->GetNode();//获取目录节点信息
        $s = implode(',',$ids);
        return view('deskHome', ['Node'=>$Node,'widgets'=>Widget::data(),'type'=>Db::name('soft_widget_home')->select(),'selected' => explode(',',$s), 'WidgetUser' =>  $s,'info'=>$WidgetUser,'layout'=>$con,'id'=>$WidgetUser['id'] ?? '']);
    }
    public function deskHomeLink($id,$nodeid){
        $info = Db::name('soft_widget_home')->find($id);
        $node_top = ['status'=>1,'data'=>'index/home?id='.$id,'name'=>$info['title'],'title'=>$info['title'],'pid'=>$nodeid,'level'=>2,'display'=>2];
        $ret = Db::name('softNode')->insertGetId($node_top);
        if ($ret) {
            return msg_return('发布成功！');
        } else {
            return msg_return($ret, 1);
        }
        return msg_return('操作成功！');
    }

    /**
     * 日程标签管理
     * @param string $id
     * @return \think\response\Json|\think\response\View
     */
    public function desk_tag($id=''){
        if ($this->request->isPost()) {
            return Widget::saveTag(input('post.'));
        }
        return view('',['role'=>\app\common\server\Role::data(),'info'=>Widget::find($id),'type'=>Widget::tag()]);
    }

    /**
     * 添加模块
     */
    public function deskAdd()
    {
        if ($this->request->isPost()) {
            $ret = Db::name('soft_widget')->insertGetId($this->request->post());
            if ($ret) {
                return msg_return('发布成功！');
            } else {
                return msg_return($ret, 1);
            }
        }
        return view('deskAdd');
    }

    /**
     * 模块预览
     */
    public function deskView($id)
    {
        $info = Db::name('soft_widget')->find($id);
        $deskData = Desk::deskData($info['widgetType'], $info['widgetData'], $info, session('softId'), session('sfotRoleId'));
        return view('deskView', ['widgetContent' => $deskData['con'], 'Js' => $deskData['jsdata']]);
    }

    /**
     * 桌面编辑
     */
    public function deskEdit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['update_time'] = time();
            $ret = Db::name('soft_widget')->update($data);
            if ($ret) {
                return msg_return('发布成功！');
            } else {
                return msg_return($ret, 1);
            }
        }
        return view('deskAdd', ['info' => Db::name('soft_widget')->find($id)]);
    }

    /**
     * 状态设置
     */
    public function status($id, $status)
    {
        $ret = Db::name('soft_widget')->update(['status' => $status, 'update_time' => time(), 'id' => $id]);
        if ($ret) {
            return msg_return('操作成功！');
        } else {
            return msg_return('操作失败', 1);
        }
    }

    /**
     * 删除设置
     */
    public function del($id)
    {
        $ret = Db::name('soft_widget')->delete($id);
        if ($ret) {
            return msg_return('删除成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }
    
    public function waf(){
        if ($this->request->isPost()) {
            $config = input('post.');
			$url = app()->getRootPath();
            arr2file($url . '/config/SysWaf.php', $config);
            $this->success('更新成功！');
        }
        return view('waf', ['w' => config('SysWaf')]);
	}

    // 清除缓存
    public function clear()
    {
        $path = App()->getRootPath() . 'runtime';
        if ($this->deleteCache($path)) {
            return msg_return('操作成功！');
        } else {
            return msg_return('删除失败', 1);
        }
    }

    // 执行删除
    private function deleteCache($path)
    {
        Cache::clear();
        $handle = opendir($path);
        while (($item = readdir($handle)) !== false) {
            if ($item != '.' && $item != '..') {
                if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                    $this->deleteCache($path . DIRECTORY_SEPARATOR . $item);
                } else {
                    if (!unlink($path . DIRECTORY_SEPARATOR . $item)) {
                        return false;
                    }
                }
            }
        }
        closedir($handle);
        return true;
    }

    public function up()
    {
        $gadmin_up_token = session('gadmin_up_token');
        $sys = [
            'server_os' => PHP_OS,//服务器ip
            'server_ip' => GetHostByName($_SERVER['SERVER_NAME']),
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'think_version' => app()->version(),
            'php_sapi_name' => PHP_SAPI,
            'db_version' => Db::query('select VERSION() as db_version')[0]['db_version'],
            'server_mac' => ''
        ];
        $user_info = [];
        if ($gadmin_up_token == '') {
            //调用官网API接口，登入账号信息
            $res = Up::glogin();
            if ($res->data->info == 0) {
                session('gadmin_up_token', $res->data->token);
                $user_info = $this->userInfo($res->data->token);
            } else {
                $this->error($res->message, url('base'));
            }
        } else {
            $user_info = $this->userInfo($gadmin_up_token);
        }
        return view('up', ['sys' => $sys, 'user' => $user_info]);
    }

    private function userInfo($token)
    {
        $headers = ['Content-Type:application/x-www-form-urlencoded', 'authentication:' . $token];
        $res_user = Up::Curl(g_cache('g_api') . '/v1/get_user', true, 'post', null, $headers);
        if ($res_user->code != 200) {
            $this->error($res_user->message);
        }
        return (array)$res_user->data;
    }

    /**
     * 数据初始化
     * @return \think\response\Json|\think\response\View|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function Initdata(){
        if ($this->request->isPost()) {
            $config = input('post.');
            $data= Initdata::getCsvData($config['file']);
            $ret = (new Initdata())->Init($config['type'],$data);
            return json($ret);
        }
        $act = input('get.act');
        if($act =='down'){
            $id = input('get.id');
			return (new Initdata())->down($id);
        }
        $table = Db::name('sfdp_design')->where('s_design',2)->order('ID desc')->select();
        return view('Initdata',['table'=>$table]);
    }

    /**
     * 添加自定义方法
     * @param string $act
     * @return array|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function custom($act=''){
        $table = Db::name('soft_custom')->find(1);
        if(!$table){
            Db::name('soft_custom')->insertGetId(['php'=>'','js'=>'','css'=>'','ver'=>time()]);
        }
        if($act!='' && $this->request->isGet()){
            return json($table[$act]);
        }
        if ($this->request->isPost()) {
            $config = input('post.');
            if($config['act']=='php'){
                $update['php'] = $config['code'];
            }
            if($config['act']=='js'){
                $update['js'] = $config['code'];
            }
            if($config['act']=='css'){
                $update['css'] = $config['code'];
            }
            $update['ver'] = time();
           $ret = Db::name('soft_custom')->where('id',1)->update($update);
            if($ret){
                $find = Db::name('soft_custom')->find(1);
                $php ='<?php
/**
 *+------------------
 * Gadmin 用户自定义函数
 *+------------------
 * uptime '.date('Y-m-d H:i:s').'
 */
 use think\facade\Db;
 ';
                if(@file_put_contents(root_path(). 'extend/custom.php' , $php.$find['php']) === false)
                {
                    return ['code'=>1,'msg'=>'写入文件失败，请检查extend/目录是否有权限'];
                }
                if(@file_put_contents(root_path(). 'public/static/custom.js' , $find['js']) === false)
                {
                    return ['code'=>1,'msg'=>'写入文件失败，请检查public/static/目录是否有权限'];
                }
                if(@file_put_contents(root_path(). 'public/static/custom.css' , $find['css']) === false)
                {
                    return ['code'=>1,'msg'=>'写入文件失败，请检查public/static/目录是否有权限'];
                }
                return msg_return('操作成功！');
            }else{
                return msg_return('更新失败！',1);
            }

        }
        return view('custom');
    }
    /**/
    public function schedule(){
        if ($this->request->isPost()) {
            return json(Schedule::Data(session('softId'),input('start'),input('end')));
        }
        return view('',['type'=>Schedule::tag(session('softId'))]);
    }

    /**
     * @param $id
     * 日程查看
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function schedule_view($id){
        return view('',['info'=>Schedule::find($id)]);
    }
    /**
     * 日程添加方法
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function schedule_add($id=''){
        if(input('type')=='del'){
            return Schedule::delData($id);
        }
        if ($this->request->isPost()) {
            return Schedule::saveData(session('softId'),input('post.'));
        }
        $info = Schedule::find($id);
        $uids = Db::name('softUser')->where('status',1)->select();
        return view('',['info'=>$info,'type'=>Schedule::tag(session('softId')),'uids'=>$uids,'uids_val'=>$info['uids_val_ids']]);
    }
    /**
     * 日程标签管理
     * @param string $id
     * @return \think\response\Json|\think\response\View
     */
    public function schedule_tag($id=''){
        if ($this->request->isPost()) {
            return Schedule::saveTag(session('softId'),input('post.'));
        }
        return view('',['info'=>Schedule::find($id,'soft_day_type'),'type'=>Schedule::tag(session('softId'))]);
    }

    /**
     * 系统方案导航主页
     * @param int $pid
     * @return \think\response\View
     */
    public function navigation($pid=0){
        return view('navigation',['pid'=>$pid,'Node'=>(new \sys\Node())->GetNode(),'type'=>Navigation::tag(session('softId')),'process_data'=>Navigation::navigation_data($pid)]);
    }

    /**
     * 保存导航接口
     * @param $act
     * @return \think\response\Json
     */
    public function navigation_save($act)
    {
        return Navigation::saveDesic(input('post.'),$act);
    }

    /**
     * 导航方案展示
     * @param $id
     * @return \think\response\View
     */
    public function navigation_show($id){
        return view('navigation_show',['process_data'=>Navigation::navigation_data($id)]);
    }

    /**
     * 导航添加
     * @param string $id
     * @return \think\response\Json|\think\response\View
     */
    public function navigation_add($id=''){
        if ($this->request->isPost()) {
            return Navigation::saveData(session('softId'),input('post.'));
        }
        return view('',['tid'=>$id,'node'=>Navigation::node($id)]);
    }

    /**
     * 导航方案管理
     * @param string $id
     * @return \think\response\Json|\think\response\View
     */
    public function navigation_tag($id=''){
        if ($this->request->isPost()) {
            return Navigation::saveTag(session('softId'),input('post.'));
        }
        return view('',['info'=>Navigation::find($id,'soft_navigation_type'),'type'=>Navigation::tag(session('softId'))]);
    }
    /**
     * 系统帮助
     */
    public function help($act=''){
        $url = g_cache('g_api') . '/v2/help?act='.$act;
        return file_get_contents($url);
    }
    /**
     * 数据回收功能
     */
    public function datar($map = []){
        if ($this->request->isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("name")) $map[] = ['table', 'like', '%'.input('name').'%'];
            $list = Db::name('soft_datarecycling')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();

            $count = Db::name('soft_datarecycling')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view();
    }
    public function sup($act='show'){
        if($act=='check'){
            (new Security())->int();
            return json(['code' => 0, 'msg' => '检查成功！']);
        }
        $u = [];
        $u['user'] = Db::name('soft_user')->count();
        $u['role'] = Db::name('soft_role')->count();
        $u['data'] =Db::connect('db_log')->name('login')->limit(10)->order('id desc')->select()->toArray();
        $u['wf'] = Db::name('wf_flow')->count();
        $u['run'] = Db::name('wf_run')->count();
        $u['runc'] = Db::name('wf_run_process')->count();
        $u['sfdp'] = Db::name('sfdp_design')->count();
        $u['ys'] = Db::name('soft_source')->count();
        $u['bb'] = Db::name('fk_data')->count() + Db::name('soft_report')->count();
        $u['err'] =Db::connect('db_log')->name('err')->count();
        $u['log'] =Db::connect('db_log')->name('log')->count();
        $u['api'] =Db::connect('db_log')->name('api')->count();
        $u['f'] =(new Security())->check();
        return view('',['u'=>$u]);
    }

    /**
     * 字段自定义模块
     * @return void
     */
    public function field($map = [])
    {
        if ($this->request->isPost()) {
            if (input("table")) $map[] = ['table', 'like', '%' . input('table') . '%'];
            if (input("title")) $map[] = ['title', 'like', '%' . input('title') . '%'];
            if (input("sid")) $map[] = ['typeid', '=', input('sid') ];
            $offset = (input('page') - 1) * input('limit');
            $list = Db::name('soft_user_field')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $status = [0 => '保存中', 1 => '已执行'];
            $type = [0 => 'input', 1 => 'select', 2 => 'radio', 2 => 'checkbox'];
            foreach ($list as $k => $v) {
                $list[$k]['typev'] = $type[$v['type']];
                $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['create_time']);
                $list[$k]['title_en'] = 'user_'.$v['title_en'].$v['id'].' '.$v['mytpe'].'('.$v['lenth'].')';
                $list[$k]['stv'] = $status[$v['is_add']];
            }
            $count = Db::name('soft_user_field')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        $tableInfo = Db::connect('mysql')->query("show table status");
        foreach($tableInfo as $k=>$v){
            if(!strstr($v['Name'], 'g_soft_') || $v['Comment']==''){
                unset($tableInfo[$k]);
            }
        }
        return view('field',['t'=>$tableInfo]);
    }

    public function field_save(){
        if(input('t')==1){
            $info = Db::name('soft_user_field')->find(input('id'));
            $sql = "ALTER TABLE {$info['table']} ADD COLUMN user_{$info['title_en']}{$info['id']} {$info['mytpe']}({$info['lenth']}) NULL COMMENT '{$info['title']}';";
            try {
                $ret = Db::execute($sql);
            } catch (\Exception $e) {

                return msg_return($e->getMessage(), 1);
            }
            Db::name('soft_user_field')->update(['id'=>input('id'),'is_add'=>1]);
            return msg_return('执行成功！');
        }


        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['uid'] = session('softId');
            $data['is_add'] = 0;
            $data['create_time'] = time();
            $ret = Db::name('soft_user_field')->insertGetId($data);
            if ($ret) {
                return msg_return('添加成功！');
            } else {
                return msg_return('添加失败', 1);
            }
        }



    }
}
