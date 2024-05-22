<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use Exception;
use think\facade\Db;
use think\facade\View;

class Desk
{
    /**
     * V6.0
     * 一键构建桌面组件
     * $sid 设计版本
     * $type 类别
     **/
    public static function buildDesk($sid,$type=0){
        $info = Db::name('sfdp_design')->find($sid);
        //卡片样式
        if($type==0){
            $widgetType = 1;
            $str = <<<str
    <a style="display: block;color: inherit;padding: 15px 0;text-align: center;text-decoration: none;cursor: pointer;" onclick=window.parent.openUrl('自定义开发','/gadmin/sfdp/index?sid={$sid}')>
    <i class="layui-icon layui-icon-face-smile" style="font-size: 30px; color: #1E9FFF;"></i>  
    <div style="margin-top: 8px;">{$info['s_title']}</div></a>
str;
        }
        //列表样式
        if($type==1){
            $widgetType = 2;
            $height = '180px';
            $str ="
<table class='layui-table'>
<tr><td>会议主题</td><td>查看</td></tr>
{volist name='list' id='k'}
<tr><td>测试标题测试标题（自行修改）</td><td><a onclick=layer_show(`查看`,'/gadmin/sfdp/view?sid=19&id=')>查看</a></td></tr>
{/volist}
</table>";
            $sql = 'select * from g_{$info.s_db} ORDER BY id limit 10 ';
        }
        //仪表盘
        if($type==2){
            $widgetType = 8;
            $height = '180px';
            $fun = 'report_'.$info['s_db'];
            Db::name('soft_source')->insertGetId(['title'=>$info['s_title'],'fun'=>$fun,'conn'=>'mysql','table'=>'g_'.$info['s_db'],'type'=>2,'field'=>'count(id)/sum(id) as value','add_time'=>date('Y-m-d H:i:s'),'add_name'=>'admin','status'=>'0','stype'=>'1','open'=>'0','where'=>'']);
            $sql = '@'.$fun;
        }
        //水波图
        if($type==3){
            $widgetType = 9;
            $height = '180px';
            $fun = 'report_'.$info['s_db'];
            Db::name('soft_source')->insertGetId(['title'=>$info['s_title'],'fun'=>$fun,'conn'=>'mysql','table'=>'g_'.$info['s_db'],'type'=>2,'field'=>'count(id)/sum(id) as value','add_time'=>date('Y-m-d H:i:s'),'add_name'=>'admin','status'=>'0','stype'=>'1','open'=>'0','where'=>'']);
            $sql = '@'.$fun;
        }
        $desk = [
            'title'=>$info['s_title'],
            'widgetTitle'=>$info['s_title'],
            'widgetHeight'=>$height ?? '95px',
            'showtitle'=>0,//是否显示标题
            'is_app'=>0,
            'widgetContent'=>'',
            'widgetWidth'=>'col-xs-3',
            'status'=>'0',
            'widgetType'=>$widgetType,
            'widgetData'=>$sql ?? '',
            'Content'=>$str ?? '',
            'update_time'=>time(),
        ];
        return Db::name('soft_widget')->insertGetId($desk);
    }

    /**
     * 保存数据
     * @param $softId
     * @param $order
     */
    public static  function saveData($softId,$order){
        if (Db::name("soft_widget_user")->where('uid',$softId)->update(['widget'=>$order])) {
            return msg_return('布局调整成功！');
        } else {
            return msg_return('布局调整失败', 1);
        }
    }
    public static function sfdpData($ids,$softId,$sfotRoleId){
        $widgetdata = Db::name('soft_widget')->where('id','in',$ids)->orderRaw('field(id,'.$ids.')')->select()->toArray();
        $jsdata ='';
        foreach($widgetdata as $k=>$v){
            /*内容转换*/
            $deskData = self::deskData($v['widgetType'],$v['widgetData'],$v,$softId,$sfotRoleId);
            $widgetdata[$k]['widgetContent'] = $deskData['con'];
            $widgetdata[$k]['jsondata'] = $deskData['data'] ?? [];
            $widgetdata[$k]['type'] = $deskData['type'];
            $jsdata .= $deskData['jsdata'];
            unset($js);
        }
        return ['data' => $widgetdata,'widgetdata' => json_encode($widgetdata),'Js' => $jsdata];
    }

    public static function HomeData($softId,$sfotRoleId,$id)
    {
        $WidgetUser = Db::name("soft_widget_home")->where('id', $id)->find();
        if(!$WidgetUser){
            echo '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>(●◡●)</h1>稍等下一下，管理员正在开发漂亮的桌面系统！</h2>';exit;
        }
        $con=[];
        $content = json_decode($WidgetUser['content'] ?? '',true);
        $layout = explode(';', $WidgetUser['layout'] ?? '');
        foreach ($layout as $k=>$v){
            $kv = explode(':',$v);
            $c = explode(',',$kv[0]);
            foreach($c as $kk=>$vv){
                $c[$kk] = [$vv,($content[$k][$kk] ?? [])];
                $ids[] = $content[$k][$kk] ?? [];
            }
            $con[]=[$kv[1],$c];
        }
        $ids =  array_unique(array_merge(...$ids));
        $widgetdata = Db::name('soft_widget')->where('id','in',$ids)->where('is_app',0)->select()->toArray();
        $jsdata ='';
        $layoutHtml = [];
        foreach($widgetdata as $k=>$v){
            /*内容转换*/
            $deskData = self::deskData($v['widgetType'],$v['widgetData'],$v,$softId,$sfotRoleId);
            $layoutHtml[$v['id']]['widgetContent'] = $deskData['con'];
            $layoutHtml[$v['id']]['widgetHeight'] = $v['widgetHeight'];
            $layoutHtml[$v['id']]['widgetTitle'] = $v['widgetTitle'];
            $layoutHtml[$v['id']]['showtitle'] = $v['showtitle'];
            $layoutHtml[$v['id']]['widgetWidth'] = $v['widgetWidth'];
            $layoutHtml[$v['id']]['jsondata'] = $deskData['data'] ?? [];
            $layoutHtml[$v['id']]['type'] = $deskData['type'];
            $jsdata .= $deskData['jsdata'];
            unset($js);
        }
        foreach ($con as $k=>$v){
            foreach($v[1] as $k2=>$v2){
                foreach($v2[1] as $k3=>$v3){
                    $con[$k][1][$k2][1][$k3] =  $layoutHtml[$v3];
                }
            }
        }
        return ['data' => $widgetdata,'widgetdata' => json_encode($widgetdata),'Js' => $jsdata,'layout'=>$con];
    }

    public static function Data($softId,$sfotRoleId,$is_app=0)
    {
        $WidgetUser = Db::name("soft_widget_user")->where('uid', $softId)->find();
        if(!$WidgetUser){
            echo '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>(●◡●)</h1>稍等下一下，管理员正在开发漂亮的桌面系统！</h2>';exit;
        }
        $con=[];
        $content = json_decode($WidgetUser['widget'] ?? '',true);
        $layout = explode(';', $WidgetUser['layout'] ?? '');
        foreach ($layout as $k=>$v){
            $kv = explode(':',$v);
            $c = explode(',',$kv[0]);
            foreach($c as $kk=>$vv){
                $c[$kk] = [$vv,($content[$k][$kk] ?? [])];
                $ids[] = $content[$k][$kk] ?? [];
            }
            $con[]=[$kv[1],$c];
        }
        $ids =  array_unique(array_merge(...$ids));
        if($is_app==1){
            $widgetdata = Db::name('soft_widget')->where('is_app',$is_app)->select()->toArray();
            }else{
            $widgetdata = Db::name('soft_widget')->where('id','in',$ids)->where('is_app',$is_app)->select()->toArray();
        }

        $jsdata ='';
        $layoutHtml = [];
        foreach($widgetdata as $k=>$v){
            /*内容转换*/
            $deskData = self::deskData($v['widgetType'],$v['widgetData'],$v,$softId,$sfotRoleId);
            $layoutHtml[$v['id']]['widgetContent'] = $deskData['con'];
            $layoutHtml[$v['id']]['widgetHeight'] = $v['widgetHeight'];
            $layoutHtml[$v['id']]['widgetTitle'] = $v['widgetTitle'];
            $layoutHtml[$v['id']]['showtitle'] = $v['showtitle'];
            $layoutHtml[$v['id']]['widgetWidth'] = $v['widgetWidth'];
            $layoutHtml[$v['id']]['jsondata'] = $deskData['data'] ?? [];
            $layoutHtml[$v['id']]['type'] = $deskData['type'];
            $jsdata .= $deskData['jsdata'];
            unset($js);
        }
        foreach ($con as $k=>$v){
            foreach($v[1] as $k2=>$v2){
                foreach($v2[1] as $k3=>$v3){
                    $con[$k][1][$k2][1][$k3] =  $layoutHtml[$v3];
                }
            }
        }
        return ['data' => $widgetdata,'widgetdata' => json_encode($widgetdata),'Js' => $jsdata,'layout'=>$con];
    }
    /**
     * $widgetType 数据类别
     * $widgetData 取值函数
     */
    public static function deskData($widgetType,$widgetData,$info,$softId,$sfotRoleId)
    {
        $desk_ver = g_cache('desktype') ?? 0;
        $color = ['#429842','#1890ff','#df5667','#888888','#25c6c8','#c87825','#333','#ea2dec','#5a98de'];
        $type  = ['','','','bar','pie','line','area','rosePlot','gauge','Liquid','',''];
        if($widgetType==1){
            $function = self::commonFun($widgetData,$softId,$sfotRoleId);
            if(strpos($widgetData,'@') == false){
                $function = $function[0] ?? [];
            }
            return ['type'=>$type[$info['widgetType']],'con'=> View::display($info['Content'], ($function ?? [])) ,'data'=>[],'jsdata'=>''];
        }
        if($widgetType==2){
            $function = self::commonFun($widgetData,$softId,$sfotRoleId);
            return ['type'=>$type[$info['widgetType']],'con'=> View::display($info['Content'], ['list'=>$function]),'data'=>[] ,'jsdata'=>''];
        }
        //轮播图
        if($widgetType==10){
            $dom = 'lunbo-'.$info['id'];
            $function = self::commonFun($widgetData,$softId,$sfotRoleId);
            $html = '';
            foreach ($function as $k=>$v){
                $html .='<div><img width="100%" src="'.$v['urls'].'"></div>';
            }
            $content = '<div class="layui-carousel" id="'.$dom.'"><div carousel-item>'.$html.'</div></div>';
            unset($html);
            unset($function);
            return ['type'=>$type[$info['widgetType']],'con'=> $content,'data'=>[] ,'jsdata'=>"layui.use(function(){var carousel = layui.carousel;carousel.render({elem: '#$dom',interval: 3000,width:'100%',height:'100%'});});"];
        }
        //视频组件
        if($widgetType==11){
            return ['type'=>$type[$info['widgetType']],'con'=>'<video class="video-js vjs-default-skin vjs-big-play-centered" width="100%" height="100%"id="my-player" controls preload="auto"><source src="'.$info['Content'].'" type="video/mp4"></source></video>','data'=>[],'jsdata'=>""];
        }
        if($desk_ver == 1){
            $function = self::commonFun($widgetData, $softId, $sfotRoleId);
            if (count($function) < 1){
                return ['type'=>$type[$info['widgetType']],'con' => '<h4>暂无数据~</h4>', 'jsdata' => "",'data'=>[]];
            }
            $data = json_encode($function);
            if($info['widgetType'] > 7  ){
                $data = $function['value'];
            }
            return ['type'=>$type[$info['widgetType']],'con' => '<div  id="g2-' . $info['id'] . '" style="height: '. $info['widgetHeight'] .'"></div>', 'data' => $data, 'jsdata' => "desk.int('" . $type[$info['widgetType']]."','g2-" . $info['id'] . "','" .$data. "');"];
        }else {
            if ($widgetType == 3) {
                $function = self::commonFun($widgetData, $softId, $sfotRoleId);
                if (count($function) < 1) {
                    return ['con' => '<h4>暂无数据~</h4>', 'jsdata' => "",'data'=>[]];
                }
                $dataHead = array_keys($function[0]);
                foreach ($dataHead as $v2) {
                    $data[] = array_values(array_column($function, $v2));
                }
                $datax = json_encode($data[0]);
                $datay = json_encode($data[1]);
                return ['con' => '<div  id="echarts-' . $info['id'] . '" style="height: 180px"></div>', 'data' => $data, 'jsdata' => "desk.bar('echarts-" . $info['id'] . "'," . $datax . "," . $datay . ");"];
            }
            if ($widgetType == 4) {
                $function = self::commonFun($widgetData, $softId, $sfotRoleId);
                return ['con' => '<div id="echarts-' . $info['id'] . '" style="height: 180px"></div>', 'data' => $function, 'jsdata' => "desk.pie('echarts-" . $info['id'] . "'," . (json_encode($function)) . ");"];
            }
            if ($widgetType == 5) {
                $function = self::commonFun($widgetData, $softId, $sfotRoleId);
                if (count($function) < 1) {
                    return ['con' => '<h4>暂无数据~</h4>', 'jsdata' => ""];
                }
                $dataHead = array_keys($function[0]);
                foreach ($dataHead as $k2 => $v2) {
                    $data[] = array_values(array_column($function, $v2));
                }
                $js = '';
                foreach ($data as $k2 => $v2) {
                    if ($k2 >= 1) {
                        $js .= "{
						  name: '" . ($dataHead[$k2] ?? '') . "',
						  type: 'line',
						  symbolSize:10,
						  symbol:'circle',
						  itemStyle: {
							color: '" . ($color[$k2] ?? '#1890ff') . "',
							borderColor: '#fff',
							borderWidth: 2,
						  },
						  data: " . json_encode($data[$k2]) . ",
						},";
                    }
                }
                $datax = json_encode($data[0]);
                return ['con' => '<div  id="echarts-' . $info['id'] . '" style="height: 180px"></div>', 'data' => $data, 'jsdata' => "desk.line('echarts-" . $info['id'] . "'," . $datax . ",[" . $js . "]);"];
            }
        }
    }
    /**
     * $funname 函数方法
     * $softId 用户id
     * $sfotRoleId 角色id
     */
    private static function commonFun($funname,$softId,$sfotRoleId){
        if($funname==''){
            return [];
        }
        if(strpos($funname,'@') !== false){
            $urldata= explode("@",$funname);
            $Source = app('app\gadmin\controller\Source');
            $data =  $Source->api($urldata[1]);
            $ret = json_decode($data->getContent(),true);
            if($ret['code'] == 1){
                echo '<h2>系统级别错误('.$urldata[1].')</h2>';exit;
            }
            $function = $ret['data'];
        }else{
            try{
                $funname = str_replace("{userid}",$softId,$funname);
                $funname = str_replace("{roleid}",$sfotRoleId,$funname);
                $function = Db::query($funname);
            }catch(Exception $e){
                return ['code'=>-1,'msg'=>'SQL_Err:'.$funname];
            }
        }
        return $function;
    }
}