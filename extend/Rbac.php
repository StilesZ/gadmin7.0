<?php

/**
 *+------------------
 * Gadmin 企业级开发平台
 *+------------------
 */

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\facade\Db;

class Rbac
{
    /**
     * 获取layUi的菜单项,共三级节点
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getLayUiMenu(): string
    {
        $roleId = session('sfotRoleId');
        $layMenu = self::queryNode($roleId, 1, 0);
        $template = '';
        if (is_array($layMenu) && count($layMenu) >= 1) {
            foreach ($layMenu as  $val) {
                $pid = $val['id'];
                $dataName = $val['name'] ?? 'unknown';
                $titleName = $val['title'];
                $icon = $val["lay_icon"];
                $template .= "<li data-name='{$dataName}' class='layui-nav-item' >
                                <a href = 'javascript:;'><i class='layui-icon layui-icon-{$icon}'></i><cite>{$titleName}</cite><span class='layui-nav-more'></span></a>
                                <dl class='layui-nav-child'>";
                $childNode = self::childMenu($pid);
                if (is_array($childNode) && count($childNode) >= 1) {
                    foreach ($childNode as  $cv) {
                        $cid = $cv['id'];
                        $cTitleName = $cv['title'];
                        $cHref = $cv['data'];
                        $icon = $cv['lay_icon'];
                        $lastNode = self::childMenu($cid);
                        if (is_array($lastNode) && count($lastNode) >= 1) {
                            $template .= "<dd>
                                        <a href='javascript:;'><i class='layui-icon layui-icon-{$icon}'></i>{$cTitleName}
                                            <span class='layui-nav-more'></span>
                                        </a>
                                      <dl class='layui-nav-child'>";
                            foreach ($lastNode as  $lv) {
                                $lTitleName = $lv['title'];
                                $href = $lv['data'];
                                $template .= "<dd><a @click='openTab(`{$lTitleName}`,`{$href}`)'>$lTitleName</a></dd>";
                            }
                            $template .= "    </dl>
                                          </dd>";
                        } else {
                            $template .= "<dd><a @click='openTab(`{$cTitleName}`,`{$cHref}`)'><i class='layui-icon layui-icon-{$icon}'></i>$cTitleName</a></dd>";
                        }
                    }
                    $template .= "</dl>";
                }
                $template .= "</dl>
                            </li>";
            }
        }
        return $template;
    }

    /**
     * VUE返回数据
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getLayuiMenuData(){
        $roleId = session('sfotRoleId');
        $layMenu = self::queryNode($roleId, 1, 0);
        if (is_array($layMenu) && count($layMenu) >= 1) {
            foreach ($layMenu as  &$val) {
                $pid = $val['id'];
                $childNode = self::childMenu($pid);
                $val['open'] = false;
                if($val['is_open']==0){
                    $val['open'] = true;
                }
                if (is_array($childNode) && count($childNode) >= 1) {
                    foreach ($childNode as  &$cv) {
                        $cid = $cv['id'];
                        $lastNode = self::childMenu($cid);
                        $cv['open'] = false;
                        if($cv['is_open']==0){
                            $cv['open'] = true;
                        }
                        if (is_array($lastNode) && count($lastNode) >= 1) {
                            foreach($lastNode as  &$v){
                                $v['selected'] = false;
                            }
                            $cv['canSelect'] = false;
                            $cv['children'] = $lastNode;
                        }else{
                            $cv['canSelect'] = true;
                            $cv['children'] = [];
                        }
                    }
                    $val['children'] = $childNode;
                }else{
                    $val['children'] = [];
                }
            }
        }
        return $layMenu;
    }

    /**
     * 查询节点
     * @param $roleId
     * @param int $level
     * @param null $pid
     * @return array
     */
    public static function queryNode($roleId, $level = 1, $pid = null): array
    {
        $softId = session('softId');
        try {
            $whereArgs = [['status', '=', 1], ['display', '=', 1], ['level', '=', $level]];
            if ($pid) {
                array_push($whereArgs, ['pid', '=', $pid]);
            }
            if (session(config('rbac.admin_auth_key')) || config('rbac.user_auth_on') == false) {
                $menu = Db::name('softNode')
                    ->where([['status', '=', 1], ['display', '=', 1], ['level', '=', $level]])
                    ->field('id,title,sid,icon,data,name,lay_icon,opentype,sort,is_page,is_open')
                    ->order('sort asc')
                    ->select()
                    ->toArray();
            } else {
                $menu1 = Db::name('softNode')->alias('a')->leftJoin('softAccess u', 'u.node_id = a.id')
                    ->where('u.role_id', $roleId)
                    ->where([['a.status', '=', 1], ['a.display', '=', 1], ['a.level', '=', $level]])
                    ->field('a.id,a.title,a.sid,a.icon,a.name,a.data,a.lay_icon,opentype,sort,is_page,is_open')
                    ->order('sort asc')
                    ->select()
                    ->toArray();
                /*增加单独用户授权 20211003*/
                $menu2 = Db::name('softNode')->alias('a')->leftJoin('softAccessUid u', 'u.node_id = a.id')
                    ->where('u.uid_id', $softId)
                    ->where([['a.status', '=', 1], ['a.display', '=', 1], ['a.level', '=', $level]])
                    ->field('a.id,a.title,a.sid,a.icon,a.name,a.data,a.lay_icon,opentype,sort,is_page,is_open')
                    ->order('sort asc')
                    ->select()
                    ->toArray();
                $menu = self::ru_merhe($menu1,$menu2);
            }
            return $menu;
        } catch (DataNotFoundException | DbException $e) {
            return [];
        }
    }
    /*二维数组按指定的键值排序*/
    static function array_sort($arr, $keys, $type = 'desc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * 获取目录方法
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getMenu(): array
    {
        $roleid = session('sfotRoleId');
        $main_menu = self::queryNode($roleid);
        foreach ($main_menu as $k => $v) {
            $pid = $v['id'];
            $datas = self::childMenu($pid);
            $sub_menu_html = '';
            if (is_array($datas) && count($datas) > 0) {
                foreach ($datas as  $_value) {
                    $sub_array = self::childMenu($_value['id']);
                    $sub_menu_html .= "<li>";
                    if (is_array($sub_array) && count($sub_array) > 1) {
                        $sub_menu_html .= "<dl class='Hui-menu'><dt class='Hui-menu-title'><i class='Hui-iconfont Hui-iconfont-{$_value['icon']}'></i>{$_value['title']}<i class='Hui-iconfont Hui-admin-menu-dropdown-arrow'>&#xe6d5;</i></dt><dd class='Hui-menu-item'><ul>";
                        foreach ($sub_array as $value) {
                            $href = empty($value['data']) ? 'javascript:void(0)' : url($value['data']);
                            $sub_menu_html .= "<li><a data-href={$href} data-title={$value['title']} href='javascript:void(0)'>{$value['title']}</a></li>";
                        }
                        $sub_menu_html .= "</ul></dd></dl></li>";
                    } else {
                        $href = empty($_value['data']) ? 'javascript:void(0)' : url($_value['data']);
                        $sub_menu_html .= "<li><a data-href='{$href}' data-title='{$_value['title']}' href='javascript:void(0)'><i class='Hui-iconfont Hui-iconfont-{$_value['icon']}'></i> {$_value['title']}</a></li> ";
                    }
                }
            }
            $main_menu[$k]['left'] = $sub_menu_html;
        }
        return $main_menu;
    }

    /**
     * 子节点获取方法
     * @param $pid
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private static function childMenu($pid): array
    {
        $pid = intval($pid);
        if (session(config('rbac.admin_auth_key')) || config('rbac.user_auth_on') == false) {
            return Db::name('softNode')
                ->where([['status', '=', 1], ['display', '=', 2], ['level', '<>', 1], ['pid', '=', $pid]])
                ->field('id,title,sid,icon,data,name,lay_icon,opentype,sort,is_page,is_open')
                ->order('sort asc')
                ->select()
                ->toArray();
        } else {
            $menu1 = Db::name('softNode')
                ->alias('a')
                ->leftJoin('softAccess u', 'u.node_id = a.id')
                ->where('u.role_id', session('sfotRoleId'))
                ->where([['a.status', '=', 1], ['a.display', '=', 2], ['a.level', '<>', 1], ['a.pid', '=', $pid]])
                ->field('a.id,a.title,a.sid,a.icon,a.data,a.name,a.lay_icon,opentype,sort,is_page,is_open')->order('sort asc')
                ->select()
                ->toArray();
            /*增加单独用户授权 20211003*/
            $menu2 = Db::name('softNode')
                ->alias('a')
                ->leftJoin('softAccessUid u', 'u.node_id = a.id')
                ->where('u.uid_id', session('softId'))
                ->where([['a.status', '=', 1], ['a.display', '=', 2], ['a.level', '<>', 1], ['a.pid', '=', $pid]])
                ->field('a.id,a.title,a.sid,a.icon,a.data,a.name,a.lay_icon,opentype,sort,is_page,is_open')->order('sort asc')
                ->select()
                ->toArray();
            return self::ru_merhe($menu1,$menu2);
        }
    }

    /**
     * RBAC 权限认证方法
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static public function checkAccess():bool
    {
        $controller = strtolower(request()->controller());
        $action = strtolower(request()->action());
        $sid = request()->param('sid');
        /*转换为认证的数据*/
        if ($sid == '') {
             $rid = request()->param('id');
            if($controller=='report' && $rid != ''){
                 $url = $controller . '/' . $action .'?id='.$rid;
                }else{
                 $url = $controller . '/' . $action;
            }
        } else {
            if($action=='wfedit'){
                $action='workflow';
            }
            $url = $controller . '/' . $action . '?sid=' . $sid;
        }
        $sfotRoleId = session('sfotRoleId');
        $softId = session('softId');
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (config('rbac.user_auth_on')) {
            /*先判断用户的数据是否在不需要认证的权限里面*/
            $nac = config('rbac.not_auth_controller');
            $naa = config('rbac.not_auth_action');
            if (in_array($controller, $nac)) {
                return true;
            }
            if (in_array($action, $naa)) {
                return true;
            }
            /*认证模式  1 - 登录认证 | 2 - 实时认证  */
            if (config('rbac.user_auth_type') == 2) {
                $data = [];
                foreach (Rbac::getAccessList($sfotRoleId,$softId) as  $v) {
                    $data[] = $v['data'];
                }
                if ((in_array($url, $data))) {
                    return true;
                }
            } else {
                foreach (session('_SOFT_ACCESS') as $v) {
                    $data[] = $v['data'];
                }
                if ((in_array($url, $data))) {
                    return true;
                }
            }
        }else{
            return true;
        }
        return false;
    }

    /**
     * 保存权限列表
     */
    static public function saveAccessList()
    {
        $softRole = session('sfotRoleId');
        $softId = session('softId');
        if (config('rbac.user_auth_type') != 2 && !session(config('rbac.admin_auth_key'))) {
            session('_SOFT_ACCESS', Rbac::getAccessList($softRole,$softId));
        }
    }

    /**
     * 获取角色权限
     * @param $softRole
     * @param $softId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static public function getAccessList($softRole,$softId): array
    {
        $access = Db::name('softAccess')->where('role_id', $softRole)->whereNotNull('data')->select()->toArray();
        $access2 = Db::name('softAccessUid')->where('uid_id', $softId)->whereNotNull('data')->select()->toArray();
        return array_merge($access,$access2);
    }

    /**
     * 数组按id合并
     * @param $a1
     * @param $a2
     * @param string $key
     * @return array
     */
    static public function ru_merhe($a1,$a2,$key='id'): array
    {
        $arr = array_merge($a1,$a2);
        $tmp_arr = array();//声明数组
        foreach($arr as $k => $v){
            if(in_array($v[$key], $tmp_arr)){
                unset($arr[$k]);//删除掉数组（$arr）里相同ID的数组
            }else{
                $tmp_arr[] = $v[$key];
            }
        }
        return array_merge(self::array_sort($arr,'sort','asc'));
    }
}
