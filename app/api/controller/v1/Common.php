<?php
/**
 *+------------------
 * Gadmin 6.0 企业级开发平台
 *+------------------
 * Copyright (c) 2021~2025 https://gadmin8.cn All rights reserved.
 */
namespace app\api\controller\v1;

use app\common\server\Msg;
use app\common\server\Schedule;
use app\Request;
use app\api\controller\Api;
use app\api\controller\Token;
use app\common\server\Desk;
use app\common\server\User;
use think\facade\Db;
use app\common\server\Upload;
use app\common\server\App as AppServer;

class Common extends Api
{   
    protected $noAuth = ['index','config','open_data'];
	public static $appsercet = '4Fz0r0r1pf3yGm1MdD5UGxiJ0pHDP';
	
	public function config(){
		$config = ['sysname'=>g_cache('app_name') ?? '系统名称未设置，请前往后台设置','app_version'=>g_cache('app_ver') ?? '系统版本为配置，请前往后台设置'];
		unset($data);
		$config['ad'] = g_cache('app_ad') ?? '';
		return returnMsg('success',200,$config);
	}

    /**
     * open_data
     * 增加对外元数据接口
     * 传入 get name
     * 传入 post where 条件
     */
    public function open_data(Request $request){
        $data = $request->get();
        $Source = app('app\gadmin\controller\Source');
        $res = json_decode(($Source->api($data['name'],'',1))->getContent(),true);
        if($res['code']==1){
            return returnMsg('对不起元素没开放！',-1);
        }
        return returnMsg('元素数据获取成功！',200,$res['data']);
    }
    public function index(Request $request)
    {
		$map['username'] = input('post.username');
		$map['status'] = 1;
        $map['is_delete'] = 0;
		$ret = User::hasUser($map,input('post.password'));
		if($ret['code'] != 0){
			return returnMsg($ret['msg'],-1);
		}
		$user = [
			'id'=>$ret['data']['id'],
			'role' => $ret['data']['role'],
			'name' => $ret['data']['username'],
            'sass_id' => $ret['data']['sass_id']
		];
        $Token   = new Token;
        $nonce=rand(12344123123,23344123123);
        $sign= makeSign(['appsercet'=>self::$appsercet,'nonce'=>$nonce,'timestamp'=>time()]);
        $Token_data = $Token->token(['appsercet'=>self::$appsercet,'nonce'=>$nonce,'timestamp'=>time(),'sign'=>$sign],$user);
        $Authorization = $user['id'].' '.base64_encode($Token_data["access_token"].':'.$user['role']);
        $ret['data']['token'] = $Authorization;
      return returnMsg('登入成功！',200,$ret['data']);
    }
	
	public function userChange(Request $request){
		$data = $request->post();
		if($data['password'] != $data['password2']){
			return returnMsg('两次密码不一致，请修改',-1);
		}
		$ret = User::userChange($this->uid,['password'=>password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12])]);
		if($ret){
			return returnMsg('修改成功，请重新登入！');
		}else{
			return returnMsg('系统错误，修改失败~',-1);
		}
	}
	
	public function getApp(){
		$roleId = $this->uid;
		$userId = $this->role;
		$sys_app = [];
		$sys_app_id =[6,13,20,21,22];
		if($userId==1 || $roleId==1){
			$sys_app = Db::name('softNode')->where([['status','=',1],['mobile','=',1],['id','in',$sys_app_id]])->field('id,title,data,sid,mobile_ico,lay_icon')->order('sort asc')->select()->toArray();
            foreach ($sys_app as $k=>$v){
                $sys_app[$k]['mobile_ico'] = 'uicon-'.$v['mobile_ico'];
            }
            $app = AppServer::appData();//获取分组的app信息
		}else{
            $app = AppServer::appData();//获取分组的app信息
        }
		return returnMsg('获取成功！',200,['sys_app'=>$sys_app,'app'=>$app]);
	}
    /**
     * 获取通讯录信息
     */
	public function getTxl(){
		return returnMsg('获取列表成功！',200,User::getUserLink());
	}
    /**
     * 获取登入信息
     */
    public function getLogin(){
        $offset = (input('pageNum') - 1) * input('pageSize');
        return returnMsg('获取列表成功！',200,User::getUserLog($this->uid,$offset,input('pageSize')));
    }
    /**
     * 获取消息接口
     */
    public function getMsg($st=0){
        $offset = (input('pageNum') - 1) * input('pageSize');
        $msg_data = User::getUserMsg($this->uid,$st,input('pageSize'),input('pageNum'));
        return returnMsg('获取列表成功！',200,$msg_data);
    }

    /***
     * 设置消息为已读
     * @return null
     */
    public function readMsg(){
        return returnMsg('消息已全部设置为已读！',200,User::readUserMsg($this->uid));
    }

    public function saveSchedule(){
        $data = [
            'title'=>input('post.title'),'start_time'=>input('post.start_time'),'end_time'=>input('post.end_time'),'theme'=>input('post.theme')
        ];
        return returnMsg('设置成功！',200,Schedule::saveData($this->uid,$data));
    }
    /**
     *获取当前月份的日程信息
     */
    public function getSchedule(){
        $ymdata = input('post.st');
        if($ymdata==1){
            $ymdata = date('Y-m');

        }
        $ym = explode('-',$ymdata);
        $y = $ym[0];
        $m = $ym[1];
        $d = date('t', strtotime($y.'-'.$m));
        $s = date('Y-m-d',mktime(23,59,59,$m,01,$y));
        $e = date('Y-m-d',mktime(23,59,59,$m,$d,$y));
        $user = Schedule::Data($this->uid,$s,$e);
        $data = [];
        foreach($user as $k=>$v){
            //$data[$k]['text'] =$v['type'];
            $data[$k]['date'] = date('Y-m-d',strtotime($v['start']));
            $data[$k]['data'] = $v['title'];
        }
        $type = Schedule::tag($this->uid);
        foreach($type as $k=>$v){
            unset($type[$k]['color']);
            unset($type[$k]['uid']);

        }
        return returnMsg($type,200,$data);
    }
	public function getDesk(){
		$data = Desk::Data($this->uid,$this->role,1);
        $ad = g_cache('app_ad') ?? '';
		return returnMsg('获取桌面系统成功！',200,['ad'=>$ad,'msgCount'=>Msg::MessageTotal(['uid'=>$this->uid]),'desk'=>$data['data']]);
	}

    /**
     * 多文件上传组件
     */
    public function uploads($id, $act = 'add')
    {
        $json = [];
        $value = input('get.value');
        if ($value != '') {
            $json = Db::name('soft_file')->where('id', 'in', $value)->field('id as fileId,original as fileName,name,size as fileSize,100 as progress, "success" as fileState')->select()->toArray();
        }
        foreach ($json as $k2 => $v2) {
            $get_extension = explode('.', $v2['name']);
            $extension = end($get_extension);
            if (in_array($extension, ['xls', 'xlsx', 'doc', 'docx'])) {
                $view = '<a target="_blank" href="' . url('viewfile', ['id' => $v2['fileId']]) . '">查看</a>';
            } else {
                $view = '';
            }
            $json[$k2]['fileState'] = '<input type="hidden" name="ids" value=' . $v2['fileId'] . '><a target="_blank" href="/' . $v2['name'] . '">查看</a> ' . $view;
        }
        return json(['code' => 0, 'data' => $json]);

    }

    /**
     * 文件上传
     */
    public function uploding()
    {
        $ret = (new Upload())->up();
        if ($ret){
            return json(['code' => 0, 'data' => $ret]);
        }else{
            return json(['code' => -1, 'data' => '上传失败，或者非法附件！']);
        }
    }

    /**
     * 附件删除
     */
    public function filedel($id)
    {
        $info = Db::name('soft_file')->find($id);
        if (!unlink($info['name'])) {
            return json(['code' => 1, 'msg' => '删除失败~']);
        } else {
            $ret = Db::name('soft_file')->delete($id);
            if (!$ret) {
                return json(['code' => -1, 'msg' => '删除数据库记录失败~']);
            }
            return json(['code' => 0, 'msg' => '删除成功~']);
        }
    }
	
}
