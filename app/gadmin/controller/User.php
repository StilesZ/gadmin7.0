<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use think\facade\Db;
use think\facade\Request;
use Dingtalk;
use Weixin;
use app\common\server\User as Userver;

class User extends Base
{
    /**
     *用户列表
     */
    public function index($map = [])
    {
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
			if (input("username")) $map[] = ['username|realname|tel', 'like', '%'.input('username').'%'];
            if (input("tel")) $map[] = ['tel', 'like', '%'.input('tel').'%'];
            if (input("role")) $map[] = ['role', '=',input('role')];
            $map[] = ['is_delete','=',0];
            $list = Db::name('softUser')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $stv = ['<span class="layui-badge layui-bg-gray" >停用</span>','<span class="layui-badge">正常</span>'];
            $lock = ['<span class="layui-badge layui-bg-gray" >正常</span>','<span class="layui-badge layui-bg-normal">锁定</span>'];

            foreach ($list as $k => $v) {

                $list[$k]['deptName'] = get_common_val('soft_dept', $v['role'], 'dept_name');
                $list[$k]['roleName'] = get_common_val('soft_role', $v['role'], 'name');
                $list[$k]['add_time'] = date('Y-m-d', $v['add_time']);
                if($v['last_login_time']==null){
                    $list[$k]['last_login_time'] = '未登录';
                }else{
                    $list[$k]['last_login_time'] = date('Y-m-d H:i:s', $v['last_login_time']);
                }
                $list[$k]['stv'] = $stv[$v['status']] ?? '';
                $list[$k]['lock'] = $lock[$v['is_lock']] ?? '';
            }
            $count = Db::name('softUser')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        $tree= Db::name('softRole')->where('id', '>', 1)->field('id,name,pid,name as title')->select()->toArray();
        return view('index',['tree'=>json_encode(g_generateTree($tree)),'dd'=>g_cache('is_dd'),'wx'=>g_cache('is_wx')]);
    }
    /**
     *树构建
     */
    public function event(){
        if ($this->request->isPost()) {
            $data = input('code');
            $str=<<<php
<?php

namespace eventsys;

use think\\facade\\Db;

class sysuser{

{$data}

}
?>
php;
            $base_dir = root_path(). 'extend/eventsys/';
            if (!function_exists($base_dir)){
                @mkdir($base_dir, 0777);
            }
            if(@file_put_contents(root_path(). 'extend/eventsys/sysuser.php' , $str) === false)
            {
                return ['code'=>1,'msg'=>'写入文件失败，请检查extend/event/目录是否有权限'];
            }
            /*尝试一下代码错误*/
            try {
                $className = '\\eventsys\\sysuser';
                new $className();
            }catch (\Throwable $e) {
                return ['code'=>1,'msg'=>'错误代码：'.$e->getMessage().'<br/>错误行号：'.$e->getLine().'<br/>错误文件：'.$e->getFile()];
            }
            return msg_return('保存成功！！');
        }
        $con = @file_get_contents(root_path(). 'extend/eventsys/sysuser.php');
        $str=<<<php
<?php

namespace eventsys;

use think\\facade\\Db;

class sysuser{
php;
        $str2=<<<php
}
?>
php;
        $con = str_replace($str,"",$con);
        $con = str_replace($str2,"",$con);
        return view('',['con'=>$con]);
    }

    /**
     *新增用户
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $hasUser = Db::name('softUser')->where('username',$data['username'])->find();
            if ($hasUser) {
                return msg_return('已存在相同的用户名称！', 1);
            }
            $password = $data['password'];
            $repassword = $data['repassword'];
            if (empty($password) || empty($repassword)) {
                return msg_return('密码必须！', 1);
            }
            if ($password != $repassword) {
                return msg_return('两次输入密码不一致！', 1);
            }
            unset($data['repassword']);
            $data['add_time'] = time();
            $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $userId = Db::name('softUser')->insertGetId($data);
            if (isset($userId)) {
                $role['user_id'] = $userId;
                $role['role_id'] = $data['role'];
                if (Db::name('softRoleUser')->insertGetId($role)) {
                    Db::name('soft_widget_user')->insertGetId(['widget' => 1, 'uid' => $userId, 'utime' => time()]);
                    /*增加新增用户后的钩子的控制*/
                    $ret_event = GyEvent('UserEvent',['act'=>'user_add_after',['data'=>$data,'pwd'=>$password]]);
                    if($ret_event['code'] == 1){
                        return json($ret_event);
                    }
                    return msg_return('添加成功！');
                } else {
                    return msg_return('用户添加成功,但角色对应关系添加失败', 1);
                }
            } else {
                return msg_return('添加用户失败', 1);
            }
        } else {
            return view('add', ['sass_id'=>'','dept_tree'=>Userver::deptTree(),'role_tree'=>Userver::roleTree(),'saas'=>Db::name('softSaas')->select(),'role' => Db::name('softRole')->select()]);
        }
    }

    /**
     *用户编辑
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $password = $data['password'];
            $repassword = $data['repassword'];
            if (!empty($password) || !empty($repassword)) {
                if ($password != $repassword) {
                    return msg_return('两次输入密码不一致！', 1);
                }
            }
            if (empty($password) && empty($repassword)) {
                unset($data['password']);
                unset($data['repassword']);
            } else {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }
            unset($data['repassword']);
            $userInfo = Db::name('softUser')->update($data);
            if (isset($userInfo)) {
                Db::name('softRoleUser')->where('user_id', $id)->update(['role_id' => $data['role']]);
                $ret_event = GyEvent('UserEvent',['act'=>'user_edit_after',['data'=>$data,'pwd'=>$password]]);
                if($ret_event['code'] == 1){
                    return json($ret_event);
                }
                return msg_return('编辑成功！');
            } else {
                return msg_return('编辑用户失败', 1);
            }
        } else {
            $info = Db::name('softUser')->find($id);
            return view('add', ['sass_id'=>$info['sass_id'],'dept_tree'=>Userver::deptTree(),'role_tree'=>Userver::roleTree(),'saas'=>Db::name('softSaas')->select(),'info' => $info, 'role' => Db::name('softRole')->select()]);
        }
    }

    /**
     *用户修改
     */
    public function change($id)
    {
        $data['status'] = input('status');
        $data['id'] = $id;
        if (Db::name('softUser')->update($data)) {
            $ret_event = GyEvent('UserEvent',['act'=>'user_status_after',['data'=>$data]]);
            if($ret_event['code'] == 1){
                return json($ret_event);
            }
            return json(['msg' => '操作成功！', 'code' => 0]);
        } else {
            return json(['msg' => '操作成功！', 'code' => 1]);
        }

    }

    /**
     *用户删除
     */
    public function del($id)
    {
        $data['is_delete'] = 1;
        $data['id'] = $id;
        if (Db::name('softUser')->update($data)) {
            $ret_event = GyEvent('UserEvent',['act'=>'user_del_after',['data'=>$data]]);
            if($ret_event['code'] == 1){
                return json($ret_event);
            }
            return msg_return('删除用户成功', 1);
        } else {
            return msg_return('删除用户失败', 1);
        }
    }
    public function lock($id)
    {
        if (Db::name('softUser')->update(['is_lock'=>0,'login_err'=>0,'id'=>$id])) {
            return msg_return('解除用户成功', 0);
        } else {
            return msg_return('解除用户失败', 1);
        }
    }
    /*钉钉功能-同步组织*/
	public function dep_link(){
		$list = Db::name('softRole')
			->where('id', '>', 1)
			->where('dept_id', null)
			->field('id,name,pid,dept_id')
			->select()->toArray();
		foreach ($list as  $v){
			/*dept id = 空的话，需要同步给钉钉*/
			if($v['pid']==0){
				$parent_id = 1;
			}else{
				$parent_id = Db::name('softRole')->where('id', $v['pid'])->value('dept_id');
			}
			$post_array = [
				"name" => $v['name'],
				'parent_id'=>$parent_id
			];
			$data = (new Dingtalk())->depCreate($post_array);
			Db::name('softRole')->where('id', $v['id'])->update(['dept_id'=>$data->dept_id]);
		}
		return json(['code' => 0, 'msg' => '同步完成!']);
	}
	/*钉钉功能-同步用户信息*/
	public function user_link(){
		$list = Db::name('softUser')
			->where('id', '>', 1)
			->where('dd_userid', null)
			->field('id,username,tel,role')
			->select()->toArray();
		foreach ($list as  $v){
			/*tel 先判断用户是否存在*/
			$res_tel = (new Dingtalk())->getbymobile($v['tel']);
			/*用户不存在，将用户信息注册给钉钉*/
			if($res_tel == -1){
				if($v['role'] == 1){
					$parent_id = 1;
					}else{
					$parent_id = Db::name('softRole')->where('id', $v['role'])->value('dept_id');
					if($parent_id == null){
						echo '组织未同步！';exit;
					}
				}
				$post_array = [
					"name" => $v['username'],
					'mobile'=>$v['tel'],
					'dept_id_list'=>$parent_id
				];
				$data = (new Dingtalk())->userCreate($post_array);
				Db::name('softUser')->where('id', $v['id'])->update(['dd_userid'=>$data->userid]);
			}else{
				Db::name('softUser')->where('id', $v['id'])->update(['dd_userid'=>$res_tel]);
			}
		}
		return json(['code' => 0, 'msg' => '同步完成!']);
	}
    /*钉钉功能-同步组织*/
    public function wxdep_link(){
        $list = Db::name('softRole')
            ->where('id', '>', 1)
            ->where('dept_wx', null)
            ->field('id,name,pid,dept_wx')
            ->select()->toArray();
        foreach ($list as  $v){
            /*dept id = 空的话，需要同步给钉钉*/
            if($v['pid']==0){
                $parent_id = 1;
            }else{
                $parent_id = Db::name('softRole')->where('id', $v['pid'])->value('dept_wx');
            }
            $post_array = [
                "name" => $v['name'],
                'parentid'=>$parent_id
            ];
            $data = (new Weixin())->depCreate($post_array);
            Db::name('softRole')->where('id', $v['id'])->update(['dept_wx'=>$data]);
        }
        return json(['code' => 0, 'msg' => '同步完成!']);
    }
	/*微信功能-同步用户信息*/
	public function wx_link(){
		$list = Db::name('softUser')
			->where('id', '>', 1)
			->where('wx_userid', null)
			->field('id,username,tel,role')
			->select()->toArray();
		foreach ($list as  $v){
			if($v['role'] == 1){
				$parent_id = 1;
			}else{
				$parent_id = Db::name('softRole')->where('id', $v['role'])->value('dept_wx');
				if($parent_id == null){
					echo '组织未同步！';exit;
				}
			}
			$wxid = $this->pingyin($v['username']);
			$post_array = [
				"userid"=>$wxid,
				"name" => $v['username'],
				'mobile'=>$v['tel'],
				'department'=>$parent_id
			];
			$data = (new Weixin())->userCreate($post_array);
			Db::name('softUser')->where('id', $v['id'])->update(['wx_userid'=>$wxid]);
		}
		return json(['code' => 0, 'msg' => '同步完成!']);
	}
	public function pingyin($name){
		$pingyin = new \pinyin();
		return $pingyin->py($name,'first').date('Ymd');
	}
	
}
