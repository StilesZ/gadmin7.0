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

namespace app\gadmin\controller;

use Exception;
use think\facade\Db;
use sfdp\service\Control as Sapi;
use app\common\server\Upgrade as Up;


class Plug extends Base
{
	/**
	 * 插件安装列表
	 * @param int $page
	 * @param int $limit
	 * @return \think\response\Json|\think\response\View
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
    public function index($page=1,$limit=10)
    {
        $gadmin_up_token = session('gadmin_up_token');
        if ($gadmin_up_token == '') {
            $res = Up::glogin();
            if ($res->data->info == 0) {
                session('gadmin_up_token', $res->data->token);
            } else {
                $this->error($res->message, url('Sys/base'));
            }
        }
		if ($this->request->isPost()) {
			$keyword = input('keyword');
			$up_info = $this->Curl('plug_list?page='.$page.'&limit='.$limit.'&keyword='.$keyword);
			if($up_info['code'] == 0){
				foreach($up_info['data'] as $k=>$v){
					$btn = '<a class="layui-btn layui-btn-warm layui-btn-xs" onclick=view("https://up.gadmin8.com/v1/plug_view?id='.$v->id.'")>查看</a>';
					$find = Db::name('soft_plug')->where('pid', $v->id)->find();
					if($find){
						$btn .='<a class="layui-btn layui-btn-danger layui-btn-xs" onclick=uni('.$v->id.')>卸载</a>';
					}else{
						$btn .='<a class="layui-btn  layui-btn-xs" onclick=ins('.$v->id.')>安装</a>';
					}
					$up_info['data'][$k]->op = $btn;
				}
			}
			return json($up_info);
		}
        return view('index', ['plug' => json_encode(Db::name('soft_plug')->column('pid'))]);
    }
	
	/**
	 * 执行插件安装
	 * @param $id
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
    public function add_plug($id)
    {
        $find = Db::name('soft_plug')->where('pid', $id)->find();
        if ($find) {
            return json(['code' => -1, 'msg' => '您已经安装啦~']);
        }
        if($id !='3'){
            $check = Db::name('soft_plug')->where('pid', 3)->find();
            if (!$check) {
                return json(['code' => -1, 'msg' => '请先安装基础插件~']);
            }
        }
        //开始请求插件信息
        $up_info = $this->Curl('plug_info?id='.$id.'&ver='.GadminVer);
        if ($up_info['code'] === -1) {
            return json(['code' => -1, 'msg' => $up_info['msg']]);
        }
        $gadmin_up_token = session('gadmin_up_token');
        $base_dir = root_path();
        $cache_dir = $base_dir . 'public' . DIRECTORY_SEPARATOR . 'plug' . DIRECTORY_SEPARATOR;
        $options['http'] = [
            'method' => 'POST',
            'header' => ['Content-type:application/x-www-form-urlencoded',
                "authentication: {$gadmin_up_token}\r\n"]
        ];
        //开始下载插件
        $context = stream_context_create($options);
        $back = Up::down_file($up_info['data'], $cache_dir, 'plug.zip', $context);
        if (empty($back)) {
			return json(['code' => -1, 'msg' => '升级包下载失败']);
		}
        //解压安装
        $zip_res = Up::open_zip($back['save_path'], $cache_dir);
        if ($zip_res == -1) {
            return json(['code' => -1, 'msg' => '解压出错']);
        }
        //检测是否安装过
        $g_info = $this->g_info();
        if ($g_info === false) {
            return json(['code' => -1, 'msg' => '插件包信息获取失败！']);
        }
        //读取配置函数
        //从配置信息中获取表信息，进行判断
        $tables = explode(",", $g_info->table);
        foreach ($tables as $value) {
            $table = config("database.connections.mysql.prefix") . $value;
            $ret = $this->query("SHOW TABLES LIKE '{$table}'");
            if ($ret && isset($ret['msg'][0])) {
                return json(['code' => -1, 'msg' => '数据库已经存在冲突，请勿重复安装！']);
            }
        }
        //Sql开始导入安装
        $res = self::install();
        $del_res = Up::deldir($cache_dir);
        if (empty($del_res)) {
            return json(['code' => -1, 'msg' => '缓存文件删除失败']);
        }
        if ($res['code'] == 0) {
            Db::name('soft_plug')->insertGetId(['pid' => $id, 'sid' => $res['msg']]);
            return json($res);
        } else {
            return json($res);
        }
    }
	
	/**
	 * 插件卸载
	 * @param $id
	 * @return string|\think\response\View
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	static function uni($id){
		$find = Db::name('soft_plug')->where('pid', $id)->find();
		if(!$find){
			return '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("未安装版本！");setTimeout("parent.layer.close(index)",300);</script>';
		}
		$info = Db::name('sfdp_design')->find($find['sid']);
		return view('uni', ['info' => $info]);
	}
	
	/**
	 * 卸载校验
	 * @param $sid
	 * @return \think\response\Json
	 */
	static function uni_check($sid){
    	/*删除设计信息*/
		Db::startTrans();
		try {
			Db::name('soft_plug')->where('sid',$sid)->delete();
			Db::name('sfdp_design')->where('id',$sid)->delete();
			$ver_ids = Db::name('sfdp_design_ver')->where('sid',$sid)->column('id');
			Db::name('sfdp_design_ver')->where('sid',$sid)->delete();
			Db::name('sfdp_field')->where('sid','in',$ver_ids)->delete();
			Db::name('sfdp_modue')->where('sid','in',$ver_ids)->delete();
			Db::name('soft_node')->where('sid',$sid)->delete();
			Db::commit();
			return msg_return('卸载成功！');
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return msg_return('卸载失败！','-1');
		}
	}
	
	/**
	 * 执行安装
	 * @return array
	 */
    static function install()
    {
        $dir = root_path() . 'public' . DIRECTORY_SEPARATOR . 'plug' . DIRECTORY_SEPARATOR;
        /*sfdp*/
        //执行安装sfdp_desgin  并取得SID
        //g_sfdp_design.sql
        $sfdp = up::get_sql(root_path() . 'public' . DIRECTORY_SEPARATOR . 'plug' . DIRECTORY_SEPARATOR . 'sfdp_design.sql');//读取SQL内容
        $sql_res = Db::execute($sfdp);//导入SQL数据
        if ($sql_res === false) {
            return ['code' => -1, 'msg' => '设计版本导入数据库失败~'];
        }
        $sid = Db::getLastInsID(Db::name("sfdp_design"));//获取导入的Sid
        /*执行版本部署，生成版本数据库*/
        $fix = Sapi::api('fix', $sid);
        $fix_res = json_decode($fix->getContent(), true);
        if ($fix_res['code'] <> 0) {
            return ['code' => -1, 'msg' => $fix_res['msg']];
        }
        $node_res = self::queryArray($sid);//导入SQL数据
        if ($node_res === false) {
            return ['code' => -1, 'msg' => '菜单信息导入失败！'];
        }
        $menu_id = Db::name('soft_node')->where('sid', $sid)->where('display', 2)->order('id desc')->value('id');//获取设计版本号
        /*桌面数据生成,报表数据设计*/
        $other_Sql = self::up_sql($dir . 'mysql' . DIRECTORY_SEPARATOR, $sid, $menu_id);
        if ($other_Sql === false) {
            return ['code' => -1, 'msg' => '导入桌面数据生成,报表数据设计SQL数据失败~'];
        }
        /*迁移脚本文件信息*/
        Up::copy_merge($dir . 'js' . DIRECTORY_SEPARATOR, root_path() . 'public' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'sfdp' . DIRECTORY_SEPARATOR);
		/*迁移PHP脚本文件信息*/
		Up::copy_merge($dir . 'php' . DIRECTORY_SEPARATOR, root_path());
        /*迁移view文件信息*/
        Up::copy_merge($dir . 'view' . DIRECTORY_SEPARATOR, root_path());
        return ['code' => 0, 'msg' => $sid];
    }

    private function g_info()
    {
        return Up::get_file(root_path() . '' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'plug' . DIRECTORY_SEPARATOR . 'ver.json');
    }
	
	/**
	 * 插件安装
	 * @param $sid
	 * @return bool
	 */
    static function queryArray($sid)
    {
        $file = root_path() . 'public' . DIRECTORY_SEPARATOR . 'plug' . DIRECTORY_SEPARATOR . 'node.sql';
        $sql_content = Up::get_sql($file);
        $sql_content = str_replace('@sid@', $sid, $sql_content);//替换sid
        $sql_arr = explode(';|', $sql_content);
        foreach ($sql_arr as $vv) {
            $sql_info = trim($vv);
            if (!empty($sql_info)) {
                $sql = trim($sql_info . ';|');
                $sql = str_replace(';|', ';', $sql);//替换sid
                $sql_res = Db::execute($sql);
                if ($sql_res === false) {
                    return false;
                }
            }
        }
        return true;
    }
	
	/**
	 * 执行安装脚本
	 * @param $sql
	 * @return array
	 */
    private function query($sql)
    {
        try {
            $data = Db::query($sql);
            return ['code' => 0, 'msg' => $data];
        } catch (Exception $e) {
            return ['code' => -1, 'msg' => 'SQL_Err:' . $sql];
        }
    }

    /**
     * 遍历执行sql文件
     */
    static function up_sql($dir, $sid, $menu_id = '')
    {
        if (trim($dir) == '') {
            return false;
        }
        $sql_file_res = Up::scan_dir($dir);
        if (empty($sql_file_res)) {
            return true;
        } else {
            foreach ($sql_file_res as $v) {
                if (!empty(strstr($v, '.sql'))) {
                    $sql_content = Up::get_sql($dir . $v);
                    $sql_content = str_replace('@sid@', $sid, $sql_content);//替换sid
                    if ($menu_id > 0) {
                        $sql_content = str_replace('@node_id@', $menu_id, $sql_content);//替换sid
                    }
                    $sql_arr = explode(';|', $sql_content);
                    foreach ($sql_arr as $vv) {
                        $sql_info = trim($vv);
                        if (!empty($sql_info)) {
                            $sql = trim($sql_info . ';|');
                            $sql = str_replace(';|', ';', $sql);//替换sid
                            $sql_res = Db::execute($sql);
                            if ($sql_res === false) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function Curl($url)
    {
        $gadmin_up_token = session('gadmin_up_token');
        $headers = ['Content-Type:application/x-www-form-urlencoded', 'authentication:' . $gadmin_up_token];
        return (array)Up::Curl(g_cache('g_api') . '/v1/' . $url, true, 'post', null, $headers);
    }
}