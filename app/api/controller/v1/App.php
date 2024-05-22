<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\api\controller\v1;

use app\Request;
use app\api\controller\Api;
use think\facade\Db;
use think\facade\View;
use think\Exception;
use app\common\server\Sfdp;
use app\common\server\Tpflow;

class App extends Api
{
	//protected $noAuth = ['index','getApp','getData','getSysCon','getWork'];
	
	public function getData(Request $request)
	{
		$data = $request->get();
		$appid = $data['appid'];
		$page = $data['page'];
		$keyword = $data['keyword'] ?? '';
		$sys_app = Db::name('softNode')->find($appid);
		if($sys_app['data']==''){
			return returnMsg('查不到此功能',-1);
		}
		if($sys_app['sid']==''){
			$init['title'] =	$sys_app['title'];
			$app_config = Db::name('soft_app_config')->where('mid',$appid)->find();
			$datas = Sfdp::data($app_config,$page,$keyword,$app_config['list']);
		}else{
			$app = explode("=",$sys_app['data']);
			$app_config = Db::name('soft_app_config')->where('mid',$appid)->find();
			if(!$app_config){
				return returnMsg('后台未开启此功能~',-1);
			}
			$init = Sfdp::init($app[1],['page'=>$page,'limit'=>10]);
			$datas = Sfdp::data($init,$page,$keyword,$app_config['list']);
		}
		return returnMsg($init['title'],200,$datas);
	}
	
	public function getSysCon($appid,$id){
        $app_config = Db::name('soft_app_config')->where('id',$appid)->find();
        if(!$app_config){
            $returnHtml = [
                'info'=>-1,
                'wf'=>[],
            ];
            return returnMsg('系统没有设计查看页面哦',-1,$returnHtml);
        }
        try {
            $data = Db::name($app_config['table'])->find($id);
        }catch(Exception $e) {
            return returnMsg('系统错误~',-1);
        }
        $string =str_replace("&#39;","'",$app_config['yw_content']);
        $config = [
            'type'          => 'Think',
            'cache_path'	      =>	 app()->getRootPath().'/runtime/cache/',
            'taglib_pre_load'     =>    'app\common\taglib\Gadmin'
        ];

        /*任务管理接口*/
        if($app_config['sid']==0){
            $data2['v'] = $data;
        }else{
            $data2 = Sfdp::sfdpView($app_config['sid'],$id);
        }
        $template = new \think\Template();
        $template->config($config);
        View::config($config);
        $log = Tpflow::logData($app_config['table'],$id);
        foreach ($log as $k=>$v){
            $log[$k]['content'] = date('Y-m-d') . ' ' .$v['content']. ' [' .$v['btn'].']'. ' [' .$v['user'].']';
            $log[$k]['clazz']='diygw-item  diy-icon-title';
        }
        /*操作日志*/
        if(empty($log)){
            $log = Db::name('soft_oplog')->where('bill_table',$app_config['table'])->where('bill_id',$id)->order('id dsec')->select()->toArray();
            foreach ($log as $k=>$v){
                $log[$k]['content'] = date('Y-m-d') . ' ' .$v['op_con']. ' [' .$v['op_type'].']';
                $log[$k]['clazz']='diygw-item  diy-icon-title';
            }
        }
        $files = json_decode($data2['files'],true);
        $filess = [];
        foreach ($files as $k=>$v){
            if($k=='ids'){
                foreach ($v as $vv){
                    $f  =   Db::name('soft_file')->where('id','in',$vv)->select()->toArray();
                    foreach ($f as $vf){
                        $filess[] = [$vf['original'],$vf['name']];
                    }
                }
            }
            if($k=='url') {
                foreach ($v as $kk=>$vv){
                    $filess[] = [$kk,$vv];
                }
            }
        }
        $returnHtml = [
            'd'=>$data2['v'],
            'f'=>$filess,
            'con'=>View::display(htmlspecialchars_decode($string), ['info'=>$data,'data'=>$data2['v'],'sub'=>$data2['sub'],'wf'=>$data2['wf']]),
            'topTitle'=>$app_config['title'],
            'log'=>$log,
            'wf'=>Tpflow::getAccess($app_config['table'],$id,$data),
            'wf_fid'=>$id,
            'wf_type'=>$app_config['table']
        ];
        return returnMsg('调用查看接口完成',200,$returnHtml);
	}
	public function getWork($st=0){
		$map[]=['f.status','=',$st];
		$mydata = Tpflow::getWork($map,input('pageNum'),input('pageSize'));
		return returnMsg('调用查看接口完成',200,$mydata);
	}
	public function wfcheck(Request $request){
		$data = $request->post();
		$mydata = Tpflow::saveWf($data);
		if($mydata=='success'){
		    return returnMsg('审核成功！');
	    	}else{
		    return returnMsg($mydata,-1);
		}
	}
}
