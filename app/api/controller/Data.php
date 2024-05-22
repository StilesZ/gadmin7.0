<?php
/**
 *+------------------
 * Gadmin 3.0 企业级开发平台
 *+------------------
 */
namespace app\api\controller;

use sfdp\adaptive\Design;
use sfdp\lib\unit;
use think\Request;
use think\facade\Db;


class Data
{
    protected $request;
    protected $password;
    protected $numb;
    protected $content;
    protected $token;
    protected $sid;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $action = $this->request->action();


        if($action != 'jump' && $action != 'sapi') {
            $this->init();
            $this->sid = $this->Info['sid'];
            /*全局脚本加载*/
            $this->g_js ='<script>var g_uid=0;g_role=0;g_username=0;g_sid='.$this->sid.';</script>';

            $sid_ver = Design::findVerWhere([['status','=',1],['sid','=',$this->sid]]);
            if(!$sid_ver){
                echo '未发现发行版本';exit;
            }
            $sid = $sid_ver['id'];
            $data = Design::getAddData($sid,1);
            $this->g_config = [
                'g_js'=>$this->g_js,
                'fun' =>unit::uJs($data['info']),
                's_type' =>$data['info']['s_type'],
                'load_file' =>$data['load_file'],
                'upload_file'=>unit::gconfig('upload_file')
            ];
            $config = Db::name('sfdp_data')->where('table',$data['info']['s_db'])->find();
            $this->password = $config['pass'];
            $this->numb = $config['numb'];
            $this->token = $config['token'];
            $this->route = $config['route'];
            $this->content = $config['content'];
            $this->g_data = $data['info']['s_field'];
            $this->s_sys_check = (json_decode($data['info']['s_field'],true))['s_sys_check'] ?? '0';
        }
    }
    private function init()
    {
        $token = $this->request->get('token');
        $info = Db::name('sfdp_data')->where('token',$token)->find();
        if(!$info){
            header("Location: /api/data/jump?info=Sorry,This Token is Illegal key");
            exit;
        }
        if($info['is_open']==0){
            header("Location: /api/data/jump?info=Sorry,This Data is Close");
            exit;
        }
        if($info['exp_time'] <= date('Y-m-d H:i:s')){
            header("Location: /api/data/jump?info=Sorry,This Data Out of date");
            exit;
        }
        return $this->Info = $info;
    }
    public function jump($info='系统已收到您的上报数据'){
        return view('/success', ['info' => $info]);
    }

    public function sapi($name){
        $info = Db::name('sfdp_data')->whereFindInSet('source',$name)->find();
        if($info){
            $Source = app('app\gadmin\controller\Source');
            return $Source->api($name);
        }else{
            return json(['code'=>1,'msg'=>'执行错误','data'=>'this,fun_name is err']);
        }
    }
}