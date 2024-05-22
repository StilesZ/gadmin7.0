<?php

namespace Fkreport\adaptive;

use Fkreport\fun\unit;

class Fun
{
    protected $mode ;
    public function  __construct(){
        if(unit::gconfig('db_mode')==1){
            $className = '\\Fkreport\\custom\\AdapteeFun';
        }else{
            $className = unit::gconfig('db_namespace').'AdapteeFun';
        }
        $this->mode = new $className();
    }
    /**
     * 查询全部设计数据
     * @param array $map
     * @param string $order
     * @return array|false
     */
    static function sapi($post)
    {
        if(isset($post['Id'])){
            $ret = self::getFun($post['get_test']);
        }
        return json(['Data'=>$ret['msg'],'Id'=>$post['Id'],'Type'=>$post['Type']]);
    }

    /*函数处理数据*/
    static function getFun($checkboxes_func,$type=1){
        //函数名转为数据信息
        $fun_mode = unit::gconfig('fun_mode') ?? 1;
        if ($fun_mode == 1 || $fun_mode == '') {
            $map[] = ['fun_name','=',$checkboxes_func];
            $hasFun = (new Fun())->mode->findWhere($map);
            if(!$hasFun){
                return json(['code'=>1,'msg'=>'禁止函数名称重复！']);
            }else{
                return Common::query($hasFun['function']);
            }
        } else {
            $className = unit::gconfig('fun_namespace');
            if (!class_exists($className)) {
                return ['code'=>1,'msg'=>unit::errMsg(3003)];
            }
            $getData = (new $className())->func($checkboxes_func,'');
        }
        if ($getData['code'] == -1) {
            return $getData;
        }
        if($type==1){
            return $getData;
        }
        $datas =$getData['msg'];
        $tpfd_data = [];
        $keys = array_keys($datas[0] ?? []);
        foreach($keys as $k=>$v){
            $tpfd_data[$v]=array_column($datas,$v);
        }
        return $tpfd_data;
    }
    /**
     * 数据添加，修改
     * @param $id
     * @param $data
     * @return false|int|string
     */
    static function save($data){
        if(!isset($data['id'])){
            $map[] = ['fun_name','=',$data['name']];
        }else{
            $map[] = [['fun_name','=',$data['name']],['id','<>',$data['id']]];
        }
        $hasname = (new Fun())->mode->findWhere($map);
        if($hasname){
            return json(['code'=>1,'msg'=>'禁止函数名称重复！']);
        }
        if(!isset($data['id'])){
            $ver = [
                'bill'=>'Fk'.date('YmdHis'),
                'title'=>$data['title'],
                'fun_name'=>$data['name'],
                'add_user'=>'Sys',
                'function'=>$data['fun'],
                'add_time'=>time()
            ];
            $ret = (new Fun())->mode->add($ver);
            if($ret){
                return json(['code'=>0,'msg'=>'操作成功！']);
            }else{
                return json(['code'=>-1,'msg'=>'更新出错']);
            }
        }else{
            $ver = [
                'id'=>$data['id'],
                'title'=>$data['title'],
                'fun_name'=>$data['name'],
                'function'=>$data['fun']
            ];
            $ret = (new Fun())->mode->save($ver);
            if($ret){
                return json(['code'=>0,'msg'=>'操作成功！']);
            }else{
                return json(['code'=>-1,'msg'=>'更新出错']);
            }
        }
    }
    /**
     * 查询全部设计数据
     * @param array $map
     * @param string $order
     * @return array|false
     */
    static function all($map=[],$order='id desc')
    {
        return (new Fun())->mode->all($map,$order);
    }

}