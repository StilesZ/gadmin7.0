<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use Agent;
use app\common\server\Config;
use app\Request;
use Rbac;
use think\Exception;
use think\facade\Db;
use think\facade\Session;
use think\captcha\facade\Captcha;
use think\facade\View;
use Dingtalk;
use Weixin;

class Login
{
    /**
     * 登入首页
     */
    public function index()
    {
        Config::Init();
        /*增加用户登入前的控制*/
        $ret_event = GyEvent('UserEvent',['act'=>'login_before']);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        View::config(['view_path' => root_path() . 'view/gadmin/' . (g_cache('view')??'layui') . '/']);//动态控制
        if(g_cache('is_dd')==1){
            $dd['is_dd'] = g_cache('is_dd');
            $dd['key'] = config('msg.d_appkey');
            $dd['url'] = urlencode(request()->domain().'/gadmin/Login/dauth');
        }else{
            $dd['is_dd'] = '';
            $dd['key'] = '';
            $dd['url'] = '';
        }
        if(g_cache('is_wx')==1){
            $wx['is_wx'] = g_cache('is_wx');
            $wx['appid'] = config('msg.w_corpid');
            $wx['agentid'] = config('msg.w_agentid');
            $wx['url'] = urlencode(request()->domain().'/gadmin/Login/wauth');
        }else{
            $wx['is_wx'] = '';
            $wx['appid'] = '';
            $wx['agentid'] = '';
            $wx['url'] = '';
        }
        return view('index', ['dd'=>$dd,'wx'=>$wx,'verify' => g_cache('verify')]);
    }
    public function wauth(Request $request){
        $code = input('get.code');
        $ret = (new Weixin())->getuserinfo_bycode($code);
        if($ret['code']==1){
            echo '微信登入失败！错误原因：'.$ret['msg'];exit;
        }
        $auth_info = Db::name('soft_user')->where('wx_userid',$ret['msg'])->find();
        if(!$auth_info){
            echo '您的账号未同步到微信系统，请联系管理员！';exit;
        }
        if($auth_info['status']==0){
            echo '您的账号已被禁用！！';exit;
        }
        /*调用登入组件*/
        $this->successLogin($auth_info);
        return redirect('/');
    }
    public function dauth(Request $request){
        $code = input('get.code');
        $ret = (new Dingtalk())->getuserinfo_bycode($code);
        if($ret['code']==1){
            echo '钉钉登入失败！错误原因：'.$ret['msg'];exit;
        }
        $auth_info = Db::name('soft_user')->where('dd_userid',$ret['msg'])->find();
        if(!$auth_info){
            echo '您的账号未同步到钉钉系统，请联系管理员！';exit;
        }
        if($auth_info['status']==0){
            echo '您的账号已被禁用！！';exit;
        }
        /*调用登入组件*/
        $this->successLogin($auth_info);
        return redirect('/');
    }
    public function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        return intval(((float)$msec + (float)$sec) * 1000);
    }
    /**
     * 权限判断无权限页面
     */
    public function noauth($title='您没有操作权限！',$btn='Sorry,notAuth')
    {
        View::config(['view_path' => root_path() . 'view/gadmin/' . g_cache('view') . '/']);//动态控制
        return view('noauth',['t'=>$title,'b'=>$btn]);
    }

    /**
     * 权限判断无权限页面
     */
    public function waf(Request $request)
    {
        View::config(['view_path' => root_path() . 'view/gadmin/' . g_cache('view') . '/']);//动态控制
        return view('waf',['ip'=>$request->ip()]);
    }

    /**
     * 登入判断系统
     */
    public function checkLogin(Request $request)
    {
        //return msg_return('我们希望给您一个更好的体验，所以，系统正在维护.....', 1);
        if ($request->isPost()) {
            $data = $request->post();
            if (g_cache('verify') == 2) {
                $captcha = input('captcha');
                if (!captcha_check($captcha)) {
                    return msg_return('验证码错误！', -1);
                }
            }
            $map['username'] = $data['username'];
            $map['status'] = 1;
            $map['is_delete'] = 0;
            $auth_info = Db::name('soft_user')->where($map)->find();
            if (null === $auth_info) {
                return msg_return('帐号不存在或已禁用！', 1);
            }else{
                //增加锁定时间10分钟
                if($auth_info['is_lock'] === 1 && (time()-$auth_info['last_login_time'])<=10*60) {
                    return msg_return('对不起您的账号已经被锁定！！', 1);
                }
                if($auth_info['login_err'] >= 5 && (time()-$auth_info['last_login_time'])<=10*60) {
                    Db::name('soft_user')->where('id', $auth_info['id'])->update(['is_lock'=>1,'last_login_ip'=>request()->ip()]);
                    return msg_return('对不起您的账号已经被锁定！！', 1);
                }
                if (!password_verify($data['password'], $auth_info['password'])) {
                    Db::name('soft_user')->where('id', $auth_info['id'])->inc('login_err')->update(['last_login_time'=>time()]);//增加错误登入次数
                    return msg_return('对不起，您输入的密码错误！！', 1);
                }
                if($auth_info['last_login_time']==''){
                    Session::set('first_login', 1);
                }
                /*调用登入组件*/
                return $this->successLogin($auth_info);
            }
        } else {
            return msg_return('非法请求！！', 1);
            throw new Exception("非法请求");
        }
    }
    protected function successLogin($auth_info){
        Session::set('softId', $auth_info['id']);
        Session::set('softSaasId', $auth_info['sass_id']);
        Session::set('sfotRoleId', $auth_info['role']);
        Session::set('sfotUserName', $auth_info['username']);
        Session::set('sfotRoleName', get_common_val('soft_role',$auth_info['role'],'name'));
        Session::set('sfotRealName', $auth_info['realname']);
        Session::set('dataAccess', $auth_info['dataaccess']);
        Session::set('softDept', $auth_info['dept_id']);
        Session::set('softDeptName', get_common_val('soft_dept',$auth_info['dept_id'],'dept_name'));
        if ($auth_info['id'] == 1) {
            Session::set(config('rbac.admin_auth_key'), true);
        }
        /*增加登入缓存数据*/
        if(!cache('softIds')){
            $sessionIds = [];
            $sessionIds[$auth_info['id']] = Session::getId();
            cache('softIds',$sessionIds);
        }else{
            $sessionIds = cache('softIds');
            $sessionIds[$auth_info['id']] = Session::getId();
            cache('softIds',$sessionIds);
        }

        Db::name('soft_user')->where('id', $auth_info['id'])->inc('login_count')->update(['is_lock'=>0,'last_login_time'=>time(),'login_err'=>0,'last_login_ip'=>request()->ip()]);
        $log['uid'] = $auth_info['id'];
        $log['username'] = $auth_info['username'];
        $log['login_ip'] = request()->ip();
        $iptoken = g_cache('iptoken');
        if($iptoken == ''){
            $log['login_location'] = request()->ip();//更新取消掉IP地址库的信息
        }else{
            $log['login_location'] = $this->getData('https://api.ip138.com/ip/?ip='.request()->ip().'&datatype=txt');//更新取消掉IP地址库的信息
            Db::name('soft_user')->where('id', $auth_info['id'])->update(['last_location'=>$log['login_location']]);
        }
        $log['login_browser'] = Agent::getBroswer();
        $log['login_os'] = Agent::getOs();
        $log['login_time'] = time();
        $ret = Db::connect('db_log')->name("login")->insert($log);
        if (!$ret) {
            return msg_return('登入日志记入失败~', 1);
        }
        /*增加用户登入后的控制*/
        $ret_event = GyEvent('UserEvent',['act'=>'login_after']);
        if($ret_event['code'] == 1){
            return json($ret_event);
        }
        Rbac::saveAccessList();
        return msg_return('登入成功！');
    }

    /**
     * 验证码生成系统
     */
    public function verify()
    {
        return Captcha::create();
    }
    private function getData($url){
        $header = array('token:'.g_cache('iptoken').'');
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
        $handles = curl_exec($ch);
        curl_close($ch);
        return $handles;
    }
}
