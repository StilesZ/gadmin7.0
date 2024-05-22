<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */

namespace app\gadmin\controller;

use app\common\server\Config;
use app\common\server\Upgrade as Up;

class Upgrade extends Base
{
    // 检测是否有新版本
    public function get_ver()
    {
        $g_info = $this->g_info();
        $ver_end = $this->Curl('get_ver?ver='.intval($g_info->version));
        if ($ver_end['code']!=200) {
            $result = ['code' => -1, 'msg' => $ver_end['msg']];
        } else {
            $last_ver = $ver_end['data'];
            if (intval($last_ver) > intval($g_info->version)) {
                $result = ['code' => 0, 'msg' => $last_ver];
            } else {
                $result = ['code' => 2, 'msg' => '已经是最新版本'];
            }
        }
        return json($result);
    }

    // 在线更新
    public function sys_update()
    {
        $g_info = $this->g_info();
        $up_info_res = $this->Curl('up_info');
        if ($up_info_res === false) {
            return json(['code' => -1, 'msg' => '获取信息失败~']);
        }
        $server = explode(",", $up_info_res['data']->up_log);
        $local_version = $g_info->version;
        $up_ver = 1;
        foreach ($server as $value) {
            if ($local_version < $value) {
                $up_ver = $value;
                break;
            }
        }
        if ($up_ver == 1) {
            return json(['code' => -1, 'msg' => '本地已经是最新版', 'data' => '']);
        }
        return $this->upinfo($up_ver);
    }

    private function upinfo($ver)
    {
        $base_dir = root_path();
        $path = $base_dir . 'public' . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        $cache_dir = $path . DIRECTORY_SEPARATOR;
        $up_info = $this->Curl('ver_info?ver=' . $ver);
        if ($up_info['code'] === -1) {
            return json(['code' => -1, 'msg' => '服务器更新包获取失败']);
        }
        $gadmin_up_token = session('gadmin_up_token');
        $options['http'] = [
            'method' => 'POST',
            'header' => ['Content-type:application/x-www-form-urlencoded',
                "authentication: {$gadmin_up_token}\r\n"]
        ];
        $context = stream_context_create($options);
        $back = Up::down_file($up_info['data']->download, $cache_dir, $ver . '.zip', $context);
        if (empty($back)) {
            return json(['code' => -1, 'msg' => '升级包下载失败']);
        }
        $zip_res = Up::open_zip($back['save_path'], $cache_dir);
        if ($zip_res == -1) {
            return json(['code' => -1, 'msg' => '解压出错']);
        }
        Up::del_files();
        $sql_res = Up::up_sql($cache_dir . 'mysql' . DIRECTORY_SEPARATOR);
        if ($sql_res === false) {
            return json(['code' => -1, 'msg' => 'sql文件写入失败']);
        }
        $file_up_res = Up::copy_merge($cache_dir . 'php' . DIRECTORY_SEPARATOR, $base_dir);
        if (empty($file_up_res)) {
            return json(['code' => -1, 'msg' => '文件移动合并失败']);
        }
        $write_res = file_put_contents($base_dir . 'ver.json', json_encode(['version' => $up_info['data']->version, 'vn' => $up_info['data']->vn, 'desc' => $up_info['data']->desc]));
        if (empty($write_res)) {
            return json(['code' => -1, 'msg' => '本地更新日志改写失败']);
        }
        $del_res = Up::deldir($cache_dir);
        if (empty($del_res)) {
            return json(['code' => -1, 'msg' => '缓存文件删除失败']);
        }
        if(file_exists(root_path().'error.log')){
            return json(['code' => -1, 'msg' => '更新失败，请查看本地根目录错误文件error.log']);
        }
        Config::Init(1);//升级完成，强制生成新的数据库配置缓存
        return json(['code' => 0, 'msg' => '升级完成']);
    }

    private function g_info()
    {
        $g_info = Up::sys_info();
        if ($g_info === false) {
            $this->error('本地版本记录文件获取失败');
        }
        return $g_info;
    }

    private function Curl($url)
    {
        $gadmin_up_token = session('gadmin_up_token');
        $headers = ['Content-Type:application/x-www-form-urlencoded', 'authentication:' . $gadmin_up_token];
        return (array)Up::Curl(g_cache('g_api') . '/v1/' . $url, true, 'post', null, $headers);
    }
}
