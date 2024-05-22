<?php

namespace sys;

use think\facade\Db;

class Wfaction {

	public function info($table){
        $pid = Db::name('softNode')->where([['name','=',$table],['status','=',1],['level','=',2]])->value('id');
        $main_menu = Db::name('softNode')->where('pid',$pid)->where('data','not in',['del','workflow'])->field('id,title,sid,data')->order('sort asc')->select()->toArray();
        $html = '';
        foreach($main_menu as $k=>$v){
            if($v['sid']>0){
                if($v['data']=='edit'){
                    $v['data'] = 'wfedit';
                }
                $url = 'sfdp@'.$v['data'].'@sid@'.$v['sid'];
            }else{
                $url = $v['data'];
            }
            $html .= '<option value="'.$url.'">'.$v['title'].'('.$url.')</option>';
        }
        return $html;
	}
}
?>