<?php
/**
 * 系统帮助服务层
 */

namespace app\common\server;
use sfdp\Api;
use think\facade\Db;
use think\facade\View;

class Help
{
    /**
     * 帮助添加/修改
     * @param $data post数据
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    static function helpAdd($data)
    {
        if(Db::name('soft_help')->where('sid',$data['sid'])->find()){
            $help = [
                'help'=>$data['help'],
                'uptime'=>time()
            ];
            $ret = Db::name('soft_help')->where('sid',$data['sid'])->update($help);
            if(!$ret){
                return false;
            }
        }else{
            $help = [
                'sid'=>$data['sid'],
                'help'=>$data['help'],
                'add_name'=>session('sfotUserName'),
                'add_time'=>date('Y-m-d H:i:s')
            ];
            $ret = Db::name('soft_help')->insertGetId($help);
            if(!$ret){
                return false;
            }
        }
        return true;
    }
	
	/**
	 * 帮助添加/修改
	 * @param $data post数据
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	static function PrintAdd($data)
	{
		if(Db::name('soft_print')->where('sid',$data['sid'])->find()){
			$help = [
				'con'=>$data['con'],
				'fun'=>$data['fun'],
				'uptime'=>time()
			];
			$ret = Db::name('soft_print')->where('sid',$data['sid'])->update($help);
			if(!$ret){
				return false;
			}
		}else{
			$help = [
				'sid'=>$data['sid'],
				'con'=>$data['con'],
				'fun'=>$data['fun'],
				'add_name'=>session('sfotUserName'),
				'add_time'=>date('Y-m-d H:i:s')
			];
			$ret = Db::name('soft_print')->insertGetId($help);
			if(!$ret){
				return false;
			}
		}
		return true;
	}
	static function helpPrint($sid)
	{
        $has = Db::name('soft_print')->where('sid',$sid)->where('type',g_cache('print'))->find();
        if(!$has){

            $config = (new Api($sid))->sfdpCurd('add', $sid);//如果没有发行版本，会直接输出：
            if($config['config']['s_type']==1){
                return json(['code' => 1, 'msg' => '编辑器表单，暂时不支持哦！']);
            }
            $data = json_decode($config['data'],true);
            if(g_cache('print')==1){
            /*界面Html还原*/
            $table ='';
            $top ='55.5';
            foreach($data['list'] as $k){
                $td ='';
                $array = array_values($k['data']);
                $left ='35.5';
                for ($x=1; $x<=$k['type']; $x++) {
                    $td_type = $array[$x-1]['td_type'] ?? '';
                    if($td_type!='group'){
                        $title = $array[$x-1]['tpfd_name'] ?? '';
                        if(isset($array[$x-1]['tpfd_db'])){
                            $value = $array[$x-1]['tpfd_db'] ?? '';
                        }else{
                            $value = '';
                        }
                        $td .= '{"options":{"left":'.$left.',"top":'.$top.',"height":34.5,"width":120,"title":"'.$title.'","field":"'.$value.'","fontWeight":"600","textContentVerticalAlign":"middle"},"printElementType":{"type":"text"}},';
                        $left = $left+125;
                    }
                }
                $top = $top+35;
                $table .= $td;
            }
            $has['con'] = '{"panels":[{"index":0, "height":297, "width":210, "paperHeader":49.5, "paperFooter":780, "printElements":['.$table.'], "paperNumberLeft":565.5, "paperNumberTop":819}]}';
            }else{
                $table ='<p style="text-align: center;"><span style="font-size: 24px;">'.$data['name'].'</span></p><table style="width: 100%" class="table table-bordered" data-sort="sortDisabled">';
                $max_ros = max(array_column($data['list'], 'type'));
                foreach($data['list'] as $k){
                    $td ='';
                    $array = array_values($k['data']);
                    for ($x=1; $x<=$k['type']; $x++) {
                        $td_type = $array[$x-1]['td_type'] ?? '';
                        if($td_type!='group'){
                            $title = $array[$x-1]['tpfd_name'] ?? '';
                            if(isset($array[$x-1]['tpfd_db'])){
                                $value = '{$data.'.($array[$x-1]['tpfd_db'] ?? '').'}';
                            }else{
                                $value = '';
                            }
                            if($x == $k['type'] & $x != $max_ros){
                                $colspan = $max_ros;
                                $td .= '<td valign="middle" style="word-break: break-all; border-color: rgb(221, 221, 221);" width="84" align="center">'.$title.'</td><td  colspan="'.(intval($colspan) - intval($k['type']) + 2).'">'.$value.'</td>';
                            }else{
                                $td .= '<td valign="middle" style="word-break: break-all; border-color: rgb(221, 221, 221);" width="84" align="center">'.$title.'</td><td class=>'.$value.'</td>';
                            }
                        }
                    }
                    $table .= '<tr>'.$td.'</tr>';
                }
                $has['con'] = $table;
            }
        }
		return $has;
	}
    static function PrintData($print,$sid,$id){
        $view = (new Api($sid))->sfdpCurd('view', $sid, $id);
        $view_cont = json_decode($view['info'],true);
        if(in_array('WorkFlow', $view_cont['tpfd_btn'])){
            $wf_log = Tpflow::logData($view_cont['name_db'],$id);
            foreach($wf_log as $v){
                $v['name'] = Db::name('soft_user')->where('id',$v['uid'])->value('username');
            }
            $wf =$wf_log;
        }else{
            $wf =[];
        }
        $log = Db::name('soft_oplog')->where('bill_table',$view_cont['name_db'])->where('bill_id',$id)->order('id dsec')->select()->toArray();
        $row = $view['row'];
        $row['act_log'] = $log;
        $sfun = [];
        if($print['fun'] <> ''){
            $Source = app('app\gadmin\controller\Source');
            $ret = json_decode(($Source->api($print['fun']))->getContent(),true);
            if($ret['code']==0){
                $sfun =$ret['data'];
            }else{
                echo $ret['msg'];exit;
            }
        }
        $row['act_fun'] = $sfun;
        return $row;
    }


	static function PrintView($sid,$id){
		$print = Db::name('soft_print')->where('sid',$sid)->find();
		if(!$print){
			return '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("未设计打印模板,系统自动关闭!");setTimeout("parent.layer.close(index)",1000);</script>';
		}
        $data2 = Sfdp::sfdpView($sid,$id);
        $info = self::PrintData($print,$sid,$id);
        $table_html =View::display(html_entity_decode($print['con']), ['info'=>$info,'data'=>$data2['v'],'sub'=>$data2['sub'],'wf'=>$data2['wf']]);
		$css = '<!DOCTYPE html>
<html lang="en"><head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
</head>
<body><!---打印开始-->
<!--startprint-->
<style>
    table td{text-align: center}
    table{border:1px;}
    table{border-collapse:collapse;}
    table tr td{padding:10px 5px;}
    table,table tr th, table tr td { border:1px solid #e2e2e2; }
</style>
<img src="/static/img/'.($info['status'] ?? 0).'.png" style=" position: absolute;right: -10px;">

' .$table_html.'

<!--endprint-->
<!---打印结束标签-->
<style>
    .btn{
        color: #fff;font-weight: 800;width:50px;height:40px;right:10px;cursor:pointer;background-color: rgb(94, 124, 224);border-color: rgb(255 255 255 / 0%);
    }
</style>
<div id="prints"> <button onclick="doPrint()" class="btn" style="position: fixed;top:15%;">打印</button> </div>
<script>
function doPrint() {
 bdhtml=window.document.body.innerHTML; sprnstr="<!--startprint-->"; eprnstr="<!--endprint-->";
 prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17);
 prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));
 window.document.body.innerHTML=prnhtml; window.print();
}
</script>
 </body>
</html>';
		echo $css;
	}
    /**
     * 帮助查看
     * @param $sid
     * @return mixed
     */
    static function helpFind($sid)
    {
        return Db::name('soft_help')->where('sid',$sid)->value('help');
    }

}