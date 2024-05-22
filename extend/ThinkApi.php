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
use think\facade\Db;

class ThinkApi{
	static function appCode(){
        $config_old = require root_path() . '/app/gadmin/config/msg.php';
        return $config_old['mname'];
    }

	/**
	 * 天气信息调用
	 * @param $city
	 * @return array
	 * @throws \think\db\exception\DbException
	 */
	static function lWeather($city){
		$logId = self::add_log('lWeather',$city);
		if(!$logId){
			return ['code'=>1,'msg'=>'接口日志记录失败！'];
		}
		$data =['city'=>$city];
		$ret = (array)self::Curl('https://api.topthink.com/weather/query?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'调用成功！','data'=>$ret['data']];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	}
	/**
	 * 二维码生成
	 * @param $text
	 * @param int $w
	 * @param string $el
	 * @return array
	 * @throws \think\db\exception\DbException
	 */
	static function qrcode($text,$w=300,$el='h'){
		$logId = self::add_log('qrcode',$text);
		if(!$logId){
			return ['code'=>1,'msg'=>'接口日志记录失败！'];
		}
		$data =['text'=>$text,'w'=>$w,'el'=>$el];
		$ret = (array)self::Curl('https://api.topthink.com/qrcode/index?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'调用成功！','data'=>$ret['data']];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	}
	
	
	/**
	 * 图片转文本接口
	 * @param $imgurl 图片的Url网址
	 * @return array
	 * @throws \think\db\exception\DbException
	 */
	static function oTxt($imgurl)
	{
		$logId = self::add_log('oTxt',$imgurl);
		if(!$logId){
			return ['code'=>1,'msg'=>'接口日志记录失败！'];
		}
		$data =['imgurl'=>$imgurl];
		$ret = (array)self::Curl('https://api.topthink.com/ocr/txt?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'调用成功！','data'=>$ret['data']];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	}
	/**
	 * 身份证识别接口
	 * @param $image 图像数据，base64编码(不包含data:image/jpeg;base64,)
	 * @param string $side front 正面，back反面
	 * @return array
	 * @throws \think\db\exception\DbException
	 */
	static function oCard($image,$side='front'){
		$logId = self::add_log('oCard',json_encode(['img'=>$image,'side'=>$side]));
		if(!$logId){
			return ['code'=>1,'msg'=>'接口日志记录失败！'];
		}
		$data =['image'=>$image,'side'=>$side];
		$ret = (array)self::Curl('https://api.topthink.com/ocr/idcard?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'调用成功！','data'=>$ret['data']];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	
	}
	/**
	 * 企业开票信息调用接口
	 * @param $keyword
	 * @return array
	 * @throws \think\db\exception\DbException
	 */
	static function eCode($keyword){
		$logId = self::add_log('eInfo',$keyword);
		if(!$logId){
			return ['code'=>1,'msg'=>'接口日志记录失败！'];
		}
		$data =['keyword'=>$keyword];
		
		$ret = (array)self::Curl('https://api.topthink.com/enterprise/creditcode?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'调用成功！','data'=>$ret['data']];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	}
	/**
	 * 企业信息精确查询
	 * @param $keyword
	 * @return array
	 * @throws \think\db\exception\DbException
	 */
	static function eInfo($keyword){
		$logId = self::add_log('eInfo',$keyword);
		if(!$logId){
			return ['code'=>1,'msg'=>'接口日志记录失败！'];
		}
		$data =['keyword'=>$keyword];
		$ret = (array)self::Curl('https://api.topthink.com/enterprise/detail_info?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'调用成功！','data'=>$ret['data']];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	}
	
	/**
	 * 短息发送统一接口
	 * @param $phone 手机号码
	 * @param $params 传递的参数信息
	 * @param $templateId 短信模板id
	 * @return array 返回结果 0,成功 1,失败
	 */
	static function sms($phone,$params,$templateId) {
		$logId = self::add_log($phone,$params);
		if(!$logId){
			return ['code'=>1,'msg'=>'短信接口日志记录失败！'];
		}
		$data =['signId'=>config('msg.mkey'),'templateId'=>$templateId,'phone'=>$phone,'params'=>$params];
		$ret = (array)self::Curl('https://api.topthink.com/sms/send?appCode='.self::appCode(),true,'post',$data);
		if($ret['code']==0 ){
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>2]);
			return ['code'=>0,'msg'=>'发送短信成功！'];
		}else{
			self::up_log($logId,['ret'=>json_encode($ret),'status'=>1]);
			return ['code'=>1,'msg'=>$ret['message']];
		}
	}
	
	/**
	 * 邮件发送统一接口
	 * @param $to 接收人
	 * @param $subject 主题
	 * @param $content 内容
	 * @return array 返回结果 0,成功 1,失败
	 */
	static function mail($to,$subject,$content)
	{
		$mail = new Mail();
		$mail->setServer(config('msg.smtp'), config('msg.euser'), config('msg.epwd'));
		$mail->setFrom(config('msg.euser'));
		$mail->setReceiver($to);
		$mail->setMailInfo($subject, $content);
		$ret = $mail->sendMail();
		if($ret){
			return ['code'=>0,'msg'=>'发送邮件成功！'];
		}else{
			return ['code'=>1,'msg'=>'发送邮件错误'];
		}
	}
	
	/**
	 * Api调用日志
	 * @param $phone 手机号码
	 * @param $params 参数信息
	 * @return false|int|string
	 */
	static function add_log($phone,$params){
		$ins =  ['phone'=>$phone,'params'=>$params,'uid'=>session('softId'),'status'=>0,'add_time'=>time()];
		$ret =  $ret = Db::connect('db_log')->name("api")->insertGetId($ins);
		if($ret){
			return $ret;
			}else{
			return false;
		}
	}
	
	/**
	 * 更新调用日志结果
	 * @param $id
	 * @param $data
	 * @return false|int
	 * @throws \think\db\exception\DbException
	 */
	static function up_log($id,$data){
		$ret =  $ret = Db::connect('db_log')->name("api")->where('id',$id)->update($data);
		if($ret){
			return $ret;
		}else{
			return false;
		}
	}
	
	/**
	 * curl 请求调用
	 * @param $url
	 * @param bool $https
	 * @param string $method
	 * @param null $data
	 * @param null $headers
	 * @return false|mixed
	 */
	static function Curl($url,$https=true,$method='get',$data=null,$headers=null){
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
			return ['code'=>1,'message'=>$str];
		}
	}
}
