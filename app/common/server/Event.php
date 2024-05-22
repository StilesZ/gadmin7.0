<?php
/**
 * 系统事件类服务层
 */

namespace app\common\server;

use think\facade\Db;

class Event
{
    /**
     * 添加/修改
     * @param $data post数据
     */
    static function Add($data)
    {
        $find = Db::name('soft_event')->where('sid',$data['sid'])->where('act',$data['act'])->find();
        if($find){
            $post = [
                'code'=>$data['code'],
                'uptime'=>time()
            ];
            $ret = Db::name('soft_event')->where('id',$find['id'])->update($post);
            if(!$ret){
                return ['code'=>1,'msg'=>'更新失败！'];
            }
        }else{
            $post = [
                'sid'=>$data['sid'],
                'act'=>$data['act'],
                'code'=>$data['code'],
                'uid'=>session('softId'),
                'uptime'=>time()
            ];
            $ret = Db::name('soft_event')->insertGetId($post);
            if(!$ret){
                return ['code'=>1,'msg'=>'更新失败！'];
            }
        }
        return self::CreatePHP($data['sid']);
    }

    /**
     * 查找已经写完的代码类
     * @param $sid
     * @param $act
     * @return array
     */
    static function find($sid,$act){
        $find = Db::name('soft_event')->where('sid',$sid)->where('act',$act)->find();
        if($find){
            return ['code'=>0,'data'=>$find['code']];
        }else{
            $notes = self::note($act);
            $data = $notes.'public function '.$act.'($params = []){



	return ["code"=>0,"msg"=>"success"];
}';
            return ['code'=>0,'data'=>$data];
        }
    }

    static function note($type){
        $notes ='';
        if($type=='before_access'){
            $notes = '/**
 * 前置权限
 */
 ';
        }
        if($type=='add_before'){
            $notes = '/**
 * 添加前事件
 * @param   $data [Post传递参数]
 */
 ';
        }
        if($type=='add_after'){
            $notes = '/**
 * 添加后事件
 * @param   id [新增后的id]
 * @param   data [Post传递参数]
 */
 ';
        }
        if($type=='edit_before'){
            $notes = '/**
 * 修改前事件
 * @param   id [id]
 * @param   data [Post传递参数]
 */
 ';
        }
        if($type=='edit_after'){
            $notes = '/**
 * 修改后事件
 * @param   id [id]
 * @param   data [Post传递参数]
 */
 ';
        }
        if($type=='check_fun'){
            $notes = '/**
 * 审核执行后
 * @param   id [id]
 * @param   status [提交的状态id] 
 */
 ';
        }
        if($type=='check_start'){
            $notes = '/**
 * 审核执行前
 * @param   id [id]
 * @param   status [提交的状态id] 
 */
 ';
        }
        if($type=='del_fun'){
            $notes = '/**
 * 删除前的操作
 * @param   id [id]
 */
 ';
        }
        if($type=='list_before'){
            $notes = '/**
 * 列表加载前
 * @param   data [传递的数据] sid/btn/data
 * @param   status [提交的状态id] 
 */
 ';
        }
        if($type=='list_after'){
            $notes = '/**
 * 列表数据请求后
 * @param   data [传递的数据] 列表数据
 * @param   id [传递的sid] 
 */
 ';
        }
        if($type=='view_after'){
            $notes = '/**
 * 查看加载后
 * @param   data [传递的数据] 查看数据
 * @param   id [传递的sid] 
 */
 ';
        }
        if($type=='import_fun'){
            $notes = '/**
 * 导入数据处理程序
 * @param   $data [列数组]
 */
 ';
        }

        return $notes;
    }

    /**
     * 创建PHP类
     * @param $sid
     * @return array
     */
    static function CreatePHP($sid){
        $find = Db::name('soft_event')->where('sid',$sid)->where('code','<>','')->field('act,code')->select()->toArray();
        $design_ver = Db::name('sfdp_design_ver')->where('sid',$sid)->where('status',1)->find();
        $class = strtolower(str_replace('_','',$design_ver['s_db']));
        $title =[
            'before_access'=>'前置权限过滤',
            'add_before'=>'添加单据前的处理方法',
            'add_after'=>'添加单据后的处理方法',
            'edit_before'=>'修改单据前的处理方法',
            'edit_after'=>'修改后的单据处理方法',
            'check_fun'=>'审核后操作方法',
            'check_start'=>'审核前操作方法',
            'del_fun'=>'删除前操作方法',
            'list_before'=>'列表数据加载前',
            'list_after'=>'列表数据加载后',
            'view_after'=>'查看数据加载后',
            'import_fun'=>'导入数据处理事件'
        ];
        $data ='';
        foreach($find as $v){
            $data .='
	'.$v['code'].'
	
';
        }
        $str=<<<php
<?php

namespace event;

use think\\facade\\Db;

class {$class}{

{$data}

}
?>
php;
        $base_dir = root_path(). 'extend/event/';
        if (!function_exists($base_dir)){
             @mkdir($base_dir, 0777);
        }
        if(@file_put_contents(root_path(). 'extend/event/'.$class.'.php' , $str) === false)
        {
            return ['code'=>1,'msg'=>'写入文件失败，请检查extend/event/目录是否有权限'];
        }
        /*尝试一下代码错误*/
        try {
            $className = '\\event\\' . $class;
            new $className();
        }catch (\Throwable $e) {
            return ['code'=>1,'msg'=>'错误代码：'.$e->getMessage().'<br/>错误行号：'.$e->getLine().'<br/>错误文件：'.$e->getFile()];
        }
        return ['code'=>0,'msg'=>'success'];
    }
}