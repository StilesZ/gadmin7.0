<?php
/**
 *+------------------
 * Gadmin 6.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;

class User
{
    static function deptTree(){
        return json_encode(Db::name('softDept')->where('id','>',1)->field('id,dept_name as title,dept_pid as pid,dept_name,dept_name as text')->select());
    }
    static function roleTree(){
        return json_encode(Db::name('softRole')->where('id','>',1)->field('id,name as title,pid,name,name as text')->select());
    }
    static function roleTree2(){
        return Db::name('softRole')->where('id','>',1)->field('id,orgname,code')->select();

    }
    /**
     * 获取用户详细信息
     * */
    static function userInfo($id){
        $info  = Db::name("softUser")->find($id);
        return $info;
    }
    /**
     * 判断用户是否存在
     * */
    static function hasUser($map,$password)
    {
        $auth_info = Db::name('soft_user')->where($map)->find();
        if (null === $auth_info) {
            return ['code'=>1,'msg'=>'帐号不存在或已禁用'];
        } else {
            if (!password_verify($password, $auth_info['password'])) {
                return ['code'=>1,'msg'=>'密码错误'];
            }
        }
        $auth_info['role_name'] = Db::name('soft_role')->where('id',$auth_info['role'])->value('name');
        unset($auth_info['password']);
        return ['code'=>0,'msg'=>'Success','data'=>$auth_info];
    }
    /**
     * 修改用户信息
     * */
    static function userChange($id,$data){
        return Db::name('soft_user')->where('id',$id)->update($data);
    }
    /**
     * 获取用户登入日志
     * */
    static function getUserLog($uid,$offset,$limit){
        return Db::connect('db_log')->name('login')->where('uid',$uid)->limit($offset, $limit)->order('id desc')->select()->toArray();
    }
    /**
     * 获取用户msg
     * */
    static function getUserMsg($uid,$st,$litmt,$page){
        return \app\common\server\Msg::mList(['uid'=>$uid,'is_read'=>$st],$litmt,$page);
    }
    /**
     * 设置用户消息已读
     * */
    static function readUserMsg($uid){
        return \app\common\server\Msg::mRead(['uid'=>$uid]);
    }
    /**
     * 获取用户关联信息
     * */
    static function getUserLink(){
        return Db::name('soft_user')
            ->alias('u')
            ->join('g_soft_role r','u.role = r.id')
            ->where('u.status',1)
            ->field('u.id,u.role,u.username,u.tel,u.mail,r.name as role_name')->select()->toArray();
    }

}