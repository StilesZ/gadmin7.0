<?php

namespace sys;

use think\facade\Db;
/**
 *+------------------
 * Gadmin 开源后台系统
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
class Node {
	
	public function SaveNode($sid,$data,$node){
		$top_id =  Db::name('softNode')->where('sid',$sid)->find();
		/*增加当设计器重新设计的时候更新Node表中的sid*/
		if($top_id){
			return ['code'=>1,'msg'=>'对不起该栏目已经生成！'];
		 }
		 $node_name = ['add'=>'添加','edit'=>'编辑','index'=>'列表','view'=>'查看','del'=>'删除','status'=>'状态','workflow'=>'工作流','import'=>'导入'];
		 $Test_controller = ucfirst($data['db_name']);
		 $node_top = ['status'=>1,'data'=>'sfdp/index?sid='.$sid,'name'=>$Test_controller,'title'=>$data['title'],'pid'=>$node,'level'=>2,'display'=>2,'sid'=>$sid];
		 $top_id =  Db::name('softNode')->insertGetId($node_top);
		 foreach($data['btn'] as $v){
			 $node_data[] = ['status'=>1,'name'=>'','title'=>$node_name[strtolower($v)],'level'=>3,'data'=>strtolower($v),'pid'=>$top_id,'sid'=>$sid];
		 }
		$ids =  Db::name('softNode')->insertAll($node_data);
		 if(!$ids){
			return ['code'=>1,'msg'=>'err'];
		 }
		 return ['code'=>0,'msg'=>'err'];
	}
	public function GetNode(){
		$main_menu = Db::name('softNode')->where([['status','=',1],['display','=',1],['level','=',1]])->field("id,title,sid,icon,'' as pid")->order('sort asc')->select()->toArray();

		$html = '';
		foreach($main_menu as $k=>$v){
			$html .= '<option value="'.$v['id'].'">'.$v['title'].'</option>';
			$sub_array = self::childMenu($v['id']);
            $main_menu[$k]['pid'] = $sub_array;
			if(is_array($sub_array) && count($sub_array)>=1){
				foreach ($sub_array as $value) {
					$html .= '<option value="'.$value['id'].'">　---'.$value['title'].'</option>';
				}
			}

			$main_menu[$k]['left'] = $sub_array;

		}
		$html .= '';

        $tree = Db::name('softNode')->where([['status','=',1],['level','in',[0,1]],['display','in',[2,1]]])->field('id,title as name,sid,pid,title')->order('sort asc')->select()->toArray();
        foreach($tree as $k=>$v){
            $sids = Db::name('softNode')->where('pid','=',$v['id'])->whereNotNull('sid')->group('sid') ->column('sid');
            if(!empty($sids)){
                $tree[$k]['sid'] = implode(',',$sids);
            }else{
                if($v['pid']==3||$v['id']==3||$v['pid']==77||$v['pid']==7){
                    unset($tree[$k]);
                }

            }
        }
        $tree[]= ['title'=>'<b style="font-size: initial;">超级表单分类</b>','id'=>1,'pid'=>0];
		return ['html'=>$html,'data'=>$main_menu,'tree'=>$tree];
	}
	private static function childMenu($pid)
    {
        $pid = intval($pid);
		return Db::name('softNode')->where([['status','=',1],['display','=',2],['level','=',0],['pid','=',$pid]])->field('id,title,sid,icon,data')->order('sort asc')->select()->toArray();
		
    }
}
?>