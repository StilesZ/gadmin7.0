<?php
namespace app\common\server;

use think\facade\Db;

/**
 * 消息服务层
 */
class Msg
{
	/**
	 * @param int $uid
	 * @param int $suid
	 * @param string $title
	 * @param string $content
	 * @param int $yw_id
	 * @param string $yw_type
	 * @param int $type 0，工作流，1，其他消息
	 * @return bool
	 */
    public static function mAdd($uid,$suid,$title, $content, $yw_id = 0,$yw_type = 'sfdp',  $type = 0)
    {
        $data = array(
            'title'             => $title,
            'content'            => $content,
            'uid'          		=> intval($uid),
			'suid'          	=> intval($suid),
            'yw_type'     => trim($yw_type),
            'yw_id'       => intval($yw_id),
            'type'              => intval($type),
            'is_read'           => 0,
            'add_time'          => time(),
        );
        $message_id = Db::name('SoftMessage')->insertGetId($data);
        if($message_id > 0)
        {
            return true;
        }
        return false;
    }
    public static function mList($where,$limit=10,$page=1,$map=[]){
		$offset = ($page - 1) * $limit;
		$list = Db::name('SoftMessage')->where('is_delete',0)->where($where)->where($map)->limit($offset, $limit)->order('id desc')->select()->toArray();
        foreach ($list as $k=>$v){
            $hasSid = Db::name('sfdp_design')->where('s_db',$v['yw_type'])->value('id');
            if($hasSid){
                $list[$k]['href'] = '/gadmin/Sfdp/view.html?sid='.$hasSid.'&id='.$v['yw_id'];
                }else{
                $list[$k]['href'] = (string)url($v['yw_type'].'/view',['id'=>$v['yw_id']]);
            }
            $list[$k]['suid'] = get_common_val('soft_user',$v['suid'],'realname');
        }

		$count = Db::name('SoftMessage')->where('is_delete',0)->where($where)->where($map)->count();
		return ['count' => $count, 'data' => $list,'code'=>0];
	}
	public static function mRead($where){
        $ret =  Db::name('SoftMessage')->where($where)->update(['is_read'=>1]);
        if($ret){
            return ['code'=>0,'msg'=>'设置成功！'];
        }else{
            return ['code'=>1,'msg'=>'更新失败！'];
        }
	}
	
	public static function mDel($where){
        $ret =  Db::name('SoftMessage')->where($where)->update(['is_delete'=>1,'is_read'=>1]);
        if($ret){
            return ['code'=>0,'msg'=>'设置成功！'];
        }else{
            return ['code'=>1,'msg'=>'更新失败！'];
        }
	}
    
    /**
     * 消息总数
     */
    public static function MessageTotal($where = [])
    {
        $total = (int) Db::name('SoftMessage')->where($where)->count();
        return ($total > 99) ? '99+' : $total;
    }
    /**
     * 消息更新已读
     */
    public static function MessageSend($where = [])
    {
        return Db::name('SoftMessage')->where($where)->update(['is_send'=>1,'send_time'=>time()]);
    }
    /***
     * 发送消息
     */
    public static function mSend($uid,$title,$content){
        $wf_type = config('msg.wf_type');//工作流发送模式-1，关闭 0，邮件，1短信
        if($wf_type =='-1'){
            return ['code'=>0,'msg'=>'消息系统关闭！'];
        }
        $userInfo = self::getUser($uid,$wf_type);
        if(!$userInfo && $userInfo == ''){
            return ['code'=>1,'msg'=>'未配置用户电话或者邮箱'];
        }
        if($wf_type == 0){//发送邮件
            $ret = ThinkApi::mail($userInfo,$title,$content);
        }
        if($wf_type == 1){//发送短信
            $ret = ThinkApi::sms($userInfo,'{"content": "内容：['.$content.']"}',config('msg.wfmid'));
        }
        if($wf_type == 2){//钉钉消息推送
            $ret = (new Dingtalk())->sendMsg($userInfo,'您需要审核：编号：['.$title.']的'.$content.'工作流业务！');
        }
        if($wf_type == 3){//钉钉消息推送
            $ret = (new Weixin())->sendMsg($userInfo,'您需要审核：编号：['.$title.']的'.$content.'工作流业务！');
        }
        if($ret['code'] == 0){
            return ['code'=>0,'msg'=>'发送成功！'];
        }else{
            return ['code'=>1,'msg'=>'发送消息失败，请通知管理员查看！'];
        }
    }
    public static function getUser($uid,$type=1){
        $userInfo = Db::name('soft_user')->find($uid);
        //如果用户已经被禁用，则直接返回错误信息！
        if($userInfo['status'] == 0){
            return false;
        }
        if($type==3){
            $info =  $userInfo['wx_userid'];
        }
        if($type==2){
            $info =  $userInfo['dd_userid'];
        }
        if($type==1){
            $info =  $userInfo['tel'];
        }
        if($type==0){
            $info =  $userInfo['mail'];
        }
        return $info;
    }
}
?>