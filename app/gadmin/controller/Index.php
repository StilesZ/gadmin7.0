<?php
/**
 *+------------------
 * Gadmin 6.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Tpflow;
use think\facade\Db;
use think\facade\Session;
use app\common\server\Desk;
use app\common\server\Msg;
use app\common\server\User;
use Rbac;
use think\response\Json;
use think\facade\view;

class Index extends Base
{
    public function index()
    {
        $Quick = Db::name("softNodeQuick")->where('uid', session('softId'))->value('data') ?? '';
        $quick_link = Db::name("SoftNode")->whereIn('id', $Quick)->select();

        if (g_cache('view') == 'layui') {
            return view('index', ['u'=>User::userInfo(session('softId')),'watermark' => g_cache('watermark'),'watermark_content' =>g_watermark_content(),'quick' => $quick_link,'msg_count'=>Msg::MessageTotal(['uid'=>session('softId'),'is_read'=>0])]);
        }else{
            return view('index', ['main_menu' => Rbac::getMenu(),'watermark' => g_cache('watermark'),'watermark_content' =>g_watermark_content(), 'quick' => $quick_link]);
        }
    }
    public function home($id){
        $desk_ver = g_cache('desktype') ?? 0;
        $desk = Desk::HomeData(session('softId'), session('sfotRoleId'),$id);
        View::assign('ver', $desk_ver);
        return view('home', $desk);
    }
    public function getMenu()
    {
        return json(['data' => Rbac::getLayuiMenuData()]);
    }
    /*系统组件管理*/
    public function welcome()
    {
        $desk_ver = g_cache('desktype') ?? 0;
        $desk = Desk::Data(session('softId'), session('sfotRoleId'));
        View::assign('ver', $desk_ver);
        return view('welcome', $desk);
    }
	
	/**
	 * @param $order 排序字段
	 * @return \think\response\Json
	 * @throws \think\db\exception\DbException
	 */
    public function save_desk($order):Json
	{
		return Desk::saveData(session('softId'),$order);
	}
	public function info(){
		if ($this->request->isPost()) {
			if (User::userChange(session('softId'),['tel'=>input('tel'),'realname'=>input('realname'),'remark'=>input('remark'),'mail'=>input('mail')])) {
				return msg_return('修改成功！');
			} else {
				return msg_return('修改失败', 1);
			}
		}else{
			return view('info', ['info' => User::userInfo(session('softId'))]);
		}
	}
    public function user()
    {
        return view('user', ['info' => User::userInfo(session('softId'))]);
    }

    public function personal(){
        $log = User::getUserLog(session('softId'),0,15);
        $mydata = Tpflow::wfMysend(1,10);
        foreach($mydata['data'] as $k=>$v){
            if($v['status']==1){
                $mydata['data'][$k]['btn'] = '<span class="layui-badge" >流程已结束</span>';
            }else{
                $mydata['data'][$k]['btn'] = '<span class="layui-badge layui-bg-blue" >流程未结束</span>';
            }
        }
        return view('', ['log'=>$log,'data'=>$mydata['data'],'info' => User::userInfo(session('softId'))]);
    }

    public function pass()
    {
        if ($this->request->isPost()) {
            $password = input('password');
            $repassword = input('repassword');
            if (!empty($password) || !empty($repassword)) {
                if ($password != $repassword) {
                    return msg_return('两次输入密码不一致！', 1);
                }
            }
            if (empty($password) && empty($repassword)) return msg_return('密码必须填写', 1);
            $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            if (User::userChange(session('softId'),$data)) {
                Session::clear();
                return msg_return('修改成功！');
            } else {
                return msg_return('修改失败', 1);
            }
        } else {
            return view('pass', ['info' => User::userInfo(session('softId'))]);
        }
    }

    public function logout()
    {
        defined('softId') or define('softId', session('softId'));
        if (softId) {
            Session::clear();
            return $this->success('登出成功！', (string)url('Login/index'));
        } else {
            return $this->error('已经登出！', (string)url('Login/index'));
        }
    }
    public function online(){
        if ($this->request->isPost()) {
            $getId = input('id');
            $res = $this->getData('http://'.$_SERVER['SERVER_NAME'].':2121?type=endid&id='.$getId);
            if($res == 'ok'){
                return msg_return('成功');
            }else{
                return msg_return('下线失败，请联系管理员',1);
            }
        }
        $uids = $this->getData('http://'.$_SERVER['SERVER_NAME'].':2121?type=alluid');
        $uids = json_decode($uids,true);
        $all_ids = [];
        foreach($uids as $k=>$v){
            $all_ids[] = $k;
        }
        $user = Db::name('soft_user')
                ->where('id','in',$all_ids)
                ->field('id,username,realname,last_location,last_login_ip,last_login_time')->select()->toArray();
        foreach($user as $k=>$v){
            $user[$k]['os'] = Db::connect('db_log')->name("login")->where('uid',$v['id'])->order('id desc')->value('CONCAT(login_os,"   ",login_browser)');
            $user[$k]['dt'] =g_diffTime(time(),$v['last_login_time']);
        }
        return view('',['u'=>$user]);
    }

    private function getData($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
        $handles = curl_exec($ch);
        curl_close($ch);
        return $handles;
    }
}
