<?php

/**
 *+------------------
 * 钉钉服务接口
 *+------------------
 */
use think\facade\Cache;

class Dingtalk
{
    private $appkey = '';

    private $agentId = '';

    private $AppSecret = '';

    function __construct()
    {
        $data = require app()->getRootPath() . '/app/gadmin/config/msg.php';
        $this->agentId = $data['d_agentid'];
        $this->appkey = $data['d_appkey'];
        $this->AppSecret = $data['d_appsecret'];
    }

    /**
     * 扫码登入主要接口
     * @param $code
     * @return array
     */
	public function getuserinfo_bycode($code){
		$timestamp = $this->msectime();
		$s = hash_hmac('sha256', $timestamp, $this->AppSecret, true);
		$signature = base64_encode($s);
		$urlencode_signature = urlencode($signature);
		$url="https://oapi.dingtalk.com/sns/getuserinfo_bycode?accessKey=".$this->appkey."&timestamp=".$timestamp."&signature=".$urlencode_signature;
		$post_array = ["tmp_auth_code" => $code];
		$post_string = json_encode($post_array);
		$ret = $this->httpClien($url,true,'post',$post_string);
		if($ret->errcode==0){
            return $this->getbyunionid($ret->user_info->unionid);
		}else{
            return ['code'=>1,'msg'=>$ret->errmsg];
		}
	}

    /**
     * 根据unionid 获取用户的 userid
     * @param $unionid
     * @return array
     */
    public function getbyunionid($unionid){
        $url="https://oapi.dingtalk.com/topapi/user/getbyunionid?access_token=".$this->gettoken();
        $ret = $this->httpClien($url,true,'post',json_encode(["unionid" => $unionid]));
        if($ret->errcode==0){
            return ['code'=>0,'msg'=>$ret->result->userid];
        }else{
            return ['code'=>1,'msg'=>$ret->errmsg];
        }
    }
    /**
     * 根据手机号判断是否有存在用户
     * @param $post
     * @return mixed
     */
    public function getbymobile($mobile){
        $url="https://oapi.dingtalk.com/topapi/v2/user/getbymobile?access_token=".$this->gettoken();
        $ret = $this->httpClien($url,true,'post',json_encode(["mobile" => $mobile]));
        if($ret->errcode==0){
            return $ret->result->userid;
        }else{
			return -1;
        }
    }
    /**
     * 钉钉创建部门
     * @param $post
     * @return mixed
     */
    public function depCreate($post){
        $url="https://oapi.dingtalk.com/topapi/v2/department/create?access_token=".$this->gettoken();
        $ret = $this->httpClien($url,true,'post',json_encode($post));
        if($ret->errcode==0){
            return $ret->result;
        }else{
            echo $ret->errmsg;exit;
        }
    }

    /**
     * 钉钉创建用户
     * @param $post
     * @return mixed
     */
    public function userCreate($post){
        $url="https://oapi.dingtalk.com/topapi/v2/user/create?access_token=".$this->gettoken();
        $ret = $this->httpClien($url,true,'post',json_encode($post));
        if($ret->errcode==0){
            return $ret->result;
        }else{
            echo $ret->errmsg;exit;
        }
    }

    /**
     * 钉钉发送消息
     * @param $touser
     * @param $content
     * @return array
     */
    public function sendMsg($touser,$content){
        $url="https://oapi.dingtalk.com/message/send?access_token=".$this->gettoken();
        $post_array = [
            "touser" => $touser,
            "agentid" => $this->agentId,
            "msgtype" => "text",
            "text" => ["content" => $content.date("Y-m-d H:i:s",time())]
        ];
        $ret = $this->httpClien($url,true,'post',json_encode($post_array));
        if($ret->errcode==0){
			return ['code'=>0,'msg'=>'推送成功！'];
        }else{
			return ['code'=>1,'msg'=>$ret->errmsg];
        }
    }

    /**
     * 获取用户列表
     * @return array
     */
    public function UserList(){
        $dep = $this->depList();
        $userlist = [];
        foreach($dep as $id){
            $url="https://oapi.dingtalk.com/user/list?access_token=".$this->gettoken()."&department_id=".$id->dept_id;
            $ret = $this->httpClien($url);
            if($ret->errcode==0){
                $userlist[$id->dept_id] = $ret->userlist;
            }
        }
        return $userlist;
    }

    /**
     * 获取用户的角色列表
     * @return mixed
     */
    public function depList(){
        $url="https://oapi.dingtalk.com/topapi/v2/department/listsub?access_token=".$this->gettoken();
        $ret = $this->httpClien($url,true,'post',json_encode(["dept_id" => 1]));
        if($ret->errcode==0){
            return $ret->result;
        }else{
            echo $ret->errmsg;exit;
        }
    }

    /**
     * 获取用户信息
     * @param string $code
     * @return false|mixed
     */
    public function getuserinfo($code=''){
        $access_token = $this->gettoken();
        $url ='https://oapi.dingtalk.com/topapi/v2/user/getuserinfo?access_token=' . $access_token;
        $ret = $this->httpClien($url,true,'post',json_encode(['code'=>$code]));
        if($ret->errcode==0){
            return $ret;
        }else{
            echo $ret->errmsg;exit;
        }
    }

    /**
     * 获取Token
     * @return mixed
     */
    private function gettoken(){
        $access_token =  Cache::get('access_token');
        if($access_token ==''){
            $url='https://oapi.dingtalk.com/gettoken?appkey='.$this->appkey.'&appsecret='.$this->AppSecret;
            $ret = $this->httpClien($url);
            if($ret->errcode==0){
                Cache::set('access_token', $ret->access_token, 7000);
                return $ret->access_token;
            }else{
                echo $ret->errmsg;exit;
            }
        }else{
            return $access_token;
        }
    }

    /**
     * 获取毫秒算法
     * @return int
     */
	private function msectime() {
		list($msec, $sec) = explode(' ', microtime());
		return intval(((float)$msec + (float)$sec) * 1000);
	}

    /**
     * CURL请求
     * @param $url
     * @param bool $https
     * @param string $method
     * @param null $data
     * @param null $headers
     * @return false|mixed
     */
    private function httpClien($url,$https=true,$method='get',$data=null,$headers=null){
        if (trim($url) == '') {
            return false;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($https === true) {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        }
        if ($method == 'post') {
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
            $headers = ['Content-Type: application/json', 'Content-Length: ' . strlen($data)];
        }
        if ($headers != null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $str = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);
        if(intval($aStatus["http_code"])==200){
            return json_decode($str);
        }else{
            return false;
        }
    }
}