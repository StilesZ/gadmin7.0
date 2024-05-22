<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
use think\facade\Db;
use think\facade\Session;

//动态加载用户自定义方法
if (file_exists(__DIR__ . "/../extend/custom.php")) {
    require __DIR__ . "/../extend/custom.php";
}

function is_signin()
{
    if(g_cache('is_login')==2){
        return true;
    }
    $softIds = cache('softIds');
    $id = session('softId');
    if($id==''){
        return true;
    }

    if($softIds[$id] == Session::getId()){
        return true;
    }else{
        return false;
    }
}
/**
 * 钩子事件定义
 * @param   [string]          $key    [钩子名称]
 * @param   [array]           $params [输入参数]
 */
function GyEvent($key, $params = [])
{
	$ret_event = event($key, $params);
	if(!$ret_event){
		return ['code'=>1,'msg'=>'执行事件失败！'];
	}
	return $ret_event[0] ?? ['code'=>0,'msg'=>'success'];
}
/**
 * 将时间戳转换为日期时间
 * @param $time
 * @param string $format
 * @return false|string
 */

function datetime($time, $format = 'Y-m-d H:i:s')
{
	$time = is_numeric($time) ? $time : strtotime($time);
	return date($format, $time);
}

/**
 * 消息返回
 * @param string $msg
 * @param int $code
 * @param array $data
 * @param string $redirect
 * @param string $alert
 * @param false $close
 * @param string $url
 * @return \think\response\Json
 */
function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
{
    $ret = ["code" => $code, "msg" => $msg, "data" => $data];
	$extend['opt'] = [
        'alert'    => $alert,
        'close'    => $close,
        'redirect' => $redirect,
        'url'      => $url,
    ];
    $ret = array_merge($ret, $extend);
    return json($ret);
}

/**
 * 数组转文件
 * @param $filename
 * @param string $arr
 */

function arr2file($filename, $arr='')
{
    if(is_array($arr)){
        $con = var_export($arr,true);
    } else{
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>";
    write_file($filename, $con);
}

/**
 * 数组转树
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
  // 创建Tree
  $tree = array();
  if(is_array($list)) {
	// 创建基于主键的数组引用
	$refer = array();
	foreach ($list as $key => $data) {
	  $refer[$data[$pk]] =& $list[$key];
	}
	foreach ($list as $key => $data) {
	  // 判断是否存在parent
	  $parentId = $data[$pid];
	  if ($root == $parentId) {
		$tree[] =& $list[$key];
	  }else{
		if (isset($refer[$parentId])) {
		  $parent =& $refer[$parentId];
		  $parent[$child][] =& $list[$key];
		}
	  }
	}
  }
  return $tree;
}

/**
 * 文件写入
 * @param $l1
 * @param string $l2
 * @return false|int
 */
function write_file($l1, $l2='')
{
    return @file_put_contents($l1, $l2);
}

/**
 * 公共调用方法
 * @param $table
 * @param $key
 * @param string $val
 * @return mixed
 */
function get_common_val($table,$key,$val='title'){
	return Db::name($table)->where('id',$key)->value($val) ?? '未关联';
}

function get_common_strs($table,$ids,$field){
    $fields = Db::name($table)->where('id','in',explode(',',$ids ?? ''))->column($field) ?? [];
    return implode(',',$fields) ?? '';
}

/**
 * 内置调试模式
 * @param $data
 */
function p($data){
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}

/**
 * 内置获取上级，下级权限代码
 * @return mixed|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 */
function dataAccess(){
		$dc = session('dataAccess');
		$role = session('sfotRoleId');
		//全局权限
		if($dc==0){
			$ids ='';
		}
		//当前角色权限
		if($dc==1){
			$uids = Db::name('soft_user')->where('find_in_set(:uid,role)',['uid'=>$role])->field('group_concat(id) as ids')->find();
			$ids =$uids['ids'];
		}
		//本人数据权限
		if($dc==2){
			$ids =session('softId');
		}
		//当前角色及下级权限
		if($dc==3){
			$members = Db::name('soft_role')->select();;
			$rold = GetTeamMember($members,$role);
			array_push($rold,session('sfotRoleId'));
			$uids = Db::name('soft_user')->where('role','in',$rold)->field('group_concat(id) as ids')->find();
			$ids =$uids['ids'];
		}
		return $ids;
}
function GetTeamMember($members, $mid) {
	$Teams=array();//最终结果
	$mids=array($mid);//第一次执行时候的用户id
	do {
		$othermids=array();
		$state=false;
		foreach ($mids as $valueone) {
			foreach ($members as $valuetwo) {
				if($valuetwo['pid']==$valueone){
					$Teams[]=$valuetwo['id'];
					$othermids[]=$valuetwo['id'];
					$state=true;
				}
			}
		}
		$mids=$othermids;
	} while ($state==true);
 
	return $Teams;
}

	function obj2arr($obj) 
	{
		return json_decode(json_encode($obj),true);
	}
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
	function commonlist($table,$map='',$field='',$limit=10,$order_by='id desc')
    {
		$list = Db::name($table)
                ->field($field)
                ->where($map)
                ->order($order_by)
				->limit($limit)
				->paginate(10);
        return $list;
	}
	/**
	* table 表名，不含表前缀
	* map   查询条件
	* field 筛选字段
	* order_by 字段排序
	**/
	function commonListData($table,$map=[],$page=1,$limit=20,$field=''){
		$offset = ($page-1)*$limit;  
		$list = Db::name($table)->where($map)->limit($offset,$limit)->field($field)->order('id desc')->select()->toArray();
		$jsondata = [];
		foreach($list as $k=>$v){
			$list[$k]['id'] = '<input type="checkbox" value="'.$v['id'].'/'.$v['status'].'" name="ids">';
			$jsondata[$k] = array_values($list[$k]);
		}
		$count = Db::name($table)->where($map)->count();
		return ['data'=>$jsondata,'count'=>$count,'list'=>$list];
	}
	/**
	* sid sfdp设计id
	* type add edit 等等的信息
	**/
	function g_btn_access($sid,$type){
	    if(session('softId')==1){
	        return true;
	    }
	    $has = Db::name('soft_access')->where('role_id',session('sfotRoleId'))->where('data','sfdp/'.$type.'?sid='.$sid)->find();
	    $has_uid = Db::name('soft_access_uid')->where('uid_id',session('softId'))->where('data','sfdp/'.$type.'?sid='.$sid)->find();
	    if($has || $has_uid){
	        return true;
	    }else{
	        return false;
	    }
	}
    /**
     * 构建Layui的树
     * $data 数组
     **/
    function g_generateTree($data)
    {
        $items = array();
        foreach ($data as $v) {
            $items[$v['id']] = $v;
        }
        $tree = array();
        foreach ($items as $k => $item) {
            if (isset($items[$item['pid']])) {
                if ($item['pid'] < 5) {
                    $items[$item['pid']]['open'] = true;
                    $items[$item['pid']]['spread'] = true;
                }
                $items[$item['pid']]['children'][] = &$items[$k];
            } else {
                $tree[] = &$items[$k];
            }
        }
        return $tree;
    }
    /**
     * 想求两个时间间隔的小时数就用:$c/(60*60)
     * PHP 计算两个时间戳之间相差的时间 时分秒
     * 功能：计算两个时间戳之间相差的日时分秒
     * $begin_time  开始时间戳
     * $end_time 结束时间戳
     */
    function g_diffTime($timestamp2,$timestamp1,$type = 'minutes')
    {
        if ($timestamp2 <= $timestamp1)
        {
            $time =  ['hours'=>0, 'minutes'=>0, 'seconds'=>0];
            return $time[$type] ?? '';
        }
        $timediff = $timestamp2 - $timestamp1;
        // 时
        $remain = $timediff%86400;
        $hours = ($remain/3600);
        // 分
        $remain = $timediff%86400;
        $mins = ($remain/60);
        // 秒
        $secs = $remain%60;
        $time = ['hours'=>$hours, 'minutes'=>$mins, 'seconds'=>$secs];
        return sprintf("%.2f", $time[$type] ?? '0.00');
    }

    function g_returnJSMsg($msg){
        echo '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("'.$msg.'");setTimeout("parent.layer.close(index)",2000);</script>';exit;
    }

    /**
     * 缓存管理
     */
    function g_cache($name = null, $value = '', $options = null, $tag = null)
    {
        // 静态存储、不用每次都从磁盘读取
        static $object_cache = [];
        // 读取数据
        if($value === '')
        {
            if(!array_key_exists($name, $object_cache))
            {
                $object_cache[$name] = cache($name, $value, $options, $tag);
            }
            return $object_cache[$name];
        }
        // 设置数据
        return cache($name, $value, $options, $tag);
    }

    /**
     * 通用状态信息
     */
    function g_common_status($st){
        $stv = [
            -1 => '<span class="layui-badge-dot layui-bg-red" ></span> 退回',
            0 => '<span class="layui-badge-dot"></span> 保存',
            1 => '<span class="layui-badge-dot layui-bg-green" ></span> 流程',
            2 => '<span class="layui-badge-dot layui-bg-blue" ></span> 通过'
        ];
        return $stv[$st] ?? 'Err';
    }
    /**
     *获取水印信息
     */
function g_watermark_content(){
    if(g_cache('watermark_content')==null){
        return '';
    }
    return str_replace(["{uid}","{role}","{date}","{name}","{rolename}"],[session('softId'),session('sfotRoleId'),date('Y-m-d'),session('sfotUserName'),session('sfotRoleName')],g_cache('watermark_content'));
}