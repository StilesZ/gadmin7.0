<?php
/**
 *+------------------
 * 钉钉服务接口
 *+------------------
 */
use think\facade\Cache;

class Weixin
{
    private $corpid = '';

    private $agentid = '';

    private $secret = '';

    private $secret_msg = '';

    function __construct()
    {
        $data = require app()->getRootPath() . '/app/gadmin/config/msg.php';
        $this->corpid = $data['w_corpid'];
        $this->agentid = $data['w_agentid'];
        $this->secret = $data['w_secret'];//通讯录的
        $this->secret_msg = $data['w_secret_msg'];//应用的
    }

    /**
     * 扫码登入主要接口
     * @param $code
     * @return array
     */
    public function getuserinfo_bycode($code){
        $url="https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=".$this->gettoken()."&code=".$code;
        $ret = $this->httpClien($url);
        if($ret->errcode==0){
            return ['code'=>0,'msg'=>$ret->UserId];
        }else{
            return ['code'=>1,'msg'=>$ret->errmsg];
        }
    }
    /**
     * 发送消息
     * @param $touser
     * @param $content
     * @return array
     */
    public function sendMsg($touser,$content){
        $url="https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->gettoken2();
        $post_array = [
            "touser" => $touser,
            "agentid" => $this->agentid,
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
     * 创建部门
     * @param $post
     * @return mixed
     */
    public function depCreate($post){
        $url="https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=".$this->gettoken2();
        $ret = $this->httpClien($url,true,'post',json_encode($post));
        if($ret->errcode==0){
            return $ret->id;
        }else{
            echo $ret->errmsg;exit;
        }
    }
    /**
     * 创建用户
     * @param $post
     * @return mixed
     */
    public function userCreate($post){
        $url="https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=".$this->gettoken();
        $ret = $this->httpClien($url,true,'post',json_encode($post));
        if($ret->errcode==0){
            return $ret->errcode;
        }else{
            return $ret->errmsg;exit;
        }
    }
    /**
     * 获取Token
     * @return mixed
     */
    private function gettoken(){
        $access_token =  Cache::get('access_token');
        if($access_token ==''){
            $url ='https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$this->corpid.'&corpsecret='.$this->secret;
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
     * 获取Token
     * @return mixed
     */
    private function gettoken2(){
        $access_token =  Cache::get('access_token2');
        if($access_token ==''){
            $url ='https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$this->corpid.'&corpsecret='.$this->secret_msg;
            $ret = $this->httpClien($url);
            if($ret->errcode==0){
                Cache::set('access_token2', $ret->access_token, 7000);
                return $ret->access_token;
            }else{
                echo $ret->errmsg;exit;
            }
        }else{
            return $access_token;
        }
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
?>