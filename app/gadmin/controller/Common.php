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

use think\facade\Db;
use think\facade\Filesystem;
use think\facade\Request;
use app\common\server\Upload;
require(root_path().'/extend/fpdf/fpdf.php');
require(root_path().'/extend/fpdi/autoload.php');
use setasign\Fpdi\Fpdi;

class Common extends Base
{
    /**
     * 设计器调用的用户信息
     * @param array $map
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function system_user($map = []){
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("username")) $map[] = ['username|realname|tel', 'like', '%'.input('username').'%'];
            $list = Db::name('softUser')->where($map)->where('is_delete',0)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            foreach ($list as $k => $v) {
                $list[$k]['roleName'] = get_common_val('soft_role', $v['role'], 'name');
                $list[$k]['add_time'] = date('Y-m-d', $v['add_time']);
            }
            $count = Db::name('softUser')->where($map)->where('is_delete',0)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view('system_user',['id'=>input('id'),'value'=>input('value')]);
    }

    /**
     * sfdp调用的 用户信息
     * @param array $map
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function system_role($map = []){
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("username")) $map[] = ['name', 'like', '%'.input('username').'%'];
            $list = Db::name('softRole')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $count = Db::name('softRole')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view('system_role',['id'=>input('id'),'value'=>input('value')]);
    }

    /**
     * 函数调用元数据信息
     * @param array $map
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function system_fun($map = []){
        if (Request::isPost()) {
            $offset = (input('page') - 1) * input('limit');
            if (input("keyword")) $map[] = ['title|fun', 'like', '%'.input('keyword').'%'];
            $list = Db::name('softSource')->where($map)->limit($offset, input('limit'))->order('id desc')->select()->toArray();
            $count = Db::name('softSource')->where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $list, 'msg' => '']);
        }
        return view('system_fun',['id'=>input('id'),'value'=>input('value')]);
    }
    public function menu()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $w['data'] = $data['widgetId'];
            $w['uid'] = session('softId');
            $w['update'] = time();
            $id = Db::name('softNodeQuick')->where('uid', session('softId'))->find();
            if ($id) {
                $w['id'] = $id['id'];
                $ret = Db::name('softNodeQuick')->update($w);
            } else {
                $ret = Db::name('softNodeQuick')->insertGetId($w);
            }
            if ($ret) {
                return msg_return('操作成功');
            } else {
                return msg_return($ret['data'], 1);
            }
        }
        $data = Db::name("softNodeQuick")->where('uid', session('softId'))->value('data');
        $list = Db::name("softAccess")->alias('a')->join('g_soft_node w', 'a.node_id = w.id')->where('a.role_id', session('sfotRoleId'))->where('w.display', '>', 0)->where('w.data', '<>', '')->whereNotNull('a.data')->field('a.node_id,w.title')->select();
        return view('menu', ['widget' => $list, 'data' => $data]);
    }

    /**
     * 单文件上传组件
     */
    public function upload($attr_id = '')
    {
        if ($this->request->isPost()) {
            $ret = (new Upload())->up(0);
            if ($ret){
                return json(['code' => 0, 'msg' => '上传成功', 'data' => $ret['src'], 'attr_id' => $attr_id]);
            }else{
                return json(['code' => 1, 'data' => '上传失败，或者非法附件！']);
            }
        }
        $id = input('id');
        return <<<php
<div class="page-container"><input type="hidden" id="callbackId" value="{$id}"><div><div id="drag">
                    <label for="file-input"><img  width="110px"src="/static/img/upload.png"></label></div>
                <input type="file" accept="*/*" name="file" id="file-input" multiple class="input-file" style="display: none">
     </div>
</div>
<script type="text/javascript" src="/static/lib/jquery/1.9.1/jquery.min.js" ></script>
<script type="text/javascript" src="/static/lib/layer/3.1.1/layer.js" ></script>
<script type="text/javascript" src="/static/lib/H5upload.js" ></script>
<script>
    $(function () {
        var callbackId = $("#callbackId").val();
        $("#file-input").tpUpload({
            url: 'upload',
            data: {a: 'a'},
            drag: '',
            start: function () {
                layer_msg = layer.msg('正在上传中…', {time: 100000000});
            },
            progress: function (loaded, total, file) {
                $('.layui-layer-msg .layui-layer-content').html('已上传' + (loaded / total * 100).toFixed(2) + '%');
            },
            success: function (ret) {
                callback(callbackId,ret);
            },
            error: function (ret) {
                layer.alert(ret);
            },
            end: function () {
                layer.close(layer_msg);
            }
        });

    });
    /**
     * 数据回调
     * @param id
     * @param value
     */
    function callback(id,ret) {
        if (window.parent.frames.length == 0){
            layer.alert('请在弹层中打开此页');
        } else {
        	if(ret.code==1){
               	 layer.alert(ret.data); return;
             }
            parent.document.getElementById(id).value = ret.data;
			parent.$("#s"+id).remove();
			var data = '<br/><b id="s'+id+'">'+ret.data+'</b>';
			parent.$('#b'+id).after(data);
			parent.$('#b'+id).html('上传成功！');
			parent.$('#b'+id).removeAttr('onclick');
			parent.layer.close(parent.layer.getFrameIndex(window.name));
        }
    }
</script>
php;
    }

    /**
     * 附件删除
     */
    public function filedel($id)
    {
        $info = Db::name('soft_file')->find($id);
        if (!unlink($info['name'])) {
            return json(['code' => 1, 'msg' => '删除失败~']);
        } else {
            $ret = Db::name('soft_file')->delete($id);
            if (!$ret) {
                return json(['code' => 1, 'msg' => '删除数据库记录失败~']);
            }
            return json(['code' => 0, 'msg' => '删除成功~']);
        }
    }

    /**
     * 多文件上传组件
     */
    public function uploads($id, $act = 'add')
    {
        $json = [];
        $value = input('get.value');
        if ($value != '') {
            $json = Db::name('soft_file')->where('id', 'in', $value)->field('id as fileId,original as fileName,name,size as fileSize,100 as progress, "success" as fileState')->select()->toArray();
        }
        if ($act == 'view') {
            foreach ($json as $k2 => $v2) {
                $get_extension = explode('.', $v2['name']);
                $extension = end($get_extension);
                if (in_array(strtolower($extension), ['xls', 'xlsx', 'doc', 'docx','pptx','ppt'])) {
                    $view = '<a onclick="select('.$v2['fileId'].')">'.$v2['fileName'].'</a>';
                } elseif(strtolower($extension)=='pdf') {
                    $view = '<a onclick="select('.$v2['fileId'].')">'.$v2['fileName'].'</a>';
                }else {
                    $view = '<a target="_blank" href="/' . $v2['name'] . '">'.$v2['fileName'].'</a>';
                }
                $json[$k2]['fileName'] = $view;
            }
            return view('down', ['did' => $id, 'json' => json_encode($json)]);
        } else {
            foreach ($json as $k => $v) {
                $json[$k]['fileState'] = '<input type="hidden" name="ids" value=' . $v['fileId'] . '><h3><i onclick=delfile(' . $v['fileId'] . ') class="Hui-iconfont Hui-iconfont-del3 layui-icon layui-icon-delete"></i></h3>';
            }
            return view('upload', ['did' => $id, 'json' => json_encode($json)]);
        }
    }
    /**
     * 文件上传
     */
    public function uploding()
    {
        $ret = (new Upload())->up();
        if ($ret){
            return json(['code' => 0, 'data' => $ret]);
        }else{
            return json(['code' => 1, 'data' => '上传失败，或者非法附件！']);
        }
    }

    /**
     * 附件阅读
     * @param $id
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function viewfile($id)
    {
        if($id==0){
            echo '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>(ô‿ô)</h1>您好，点击附件列表可快速阅读或下载附件信息~</h2>';exit;
        }
        $info = Db::name('soft_file')->find($id);
        $get_extension = explode('\\', $info['name']);
        $extension = end($get_extension);
        $mode = g_cache('viewmode');
        //转换阅读模式
        if ($mode == 1) {
            $file_url = app()->getRootPath() . 'public/' . $info['name'];
            if (!file_exists($file_url)) {
                echo '<h2>对不起，文件不存在~</h2>';exit;
            }
            $get_extension = explode('.', $extension);
            if(strtolower($get_extension['1'])<>'pdf'){
                $file_out = app()->getRootPath() . 'public/convert/';
                if (file_exists($file_out . $get_extension[0] . '.pdf')) {
                    $url = '/convert/' . $get_extension[0] . '.pdf';
                } else {
                    $this->OfficeConverter($file_url, $file_out);
                    $url = '/convert/' . $get_extension[0] . '.pdf';
                }
            }else{
                $url = '/'.$info['name'];
            }
        } else {
            $url = g_cache('voffice') . 'http://' . $_SERVER['HTTP_HOST'] . '/' . $info['name'];
        }
        $get_extension2 = explode('/', $get_extension[0]);
        $url = str_replace("\\",'/',$url);
        if (!file_exists(root_path() .'public'.$url)) {
            echo '<h4>对不起，文件不存在~</h4>您可以点击下载原为文件-><a href="/'.$info['name'].'">下载</a>';exit;
        }
        //判断是否开启水印
        if(g_cache('watermark')==1){
            $worter_data =g_watermark_content();
            $pdf= new Fpdi();
            $file = root_path() .'public'.$url;
            //获取页数
            $pageCount = $pdf->setSourceFile($file);
            //遍历所有页面
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++){
                //导入页面
                $templateId = $pdf->importPage($pageNo);
                //获取导入页面的大小
                $size = $pdf->getTemplateSize($templateId);
                //创建页面（横向或纵向取决于导入的页面大小）
                if ($size['width'] > $size['height']){
                    $pdf->AddPage('L', array($size['width'], $size['height']));
                }else {
                    $pdf->AddPage('P', array($size['width'], $size['height']));
                }
                //使用导入的页面
                $pdf->useTemplate($templateId);
                $pdf->SetFont('helvetica','','15');
                $pdf->SetFont('Arial');
                $pdf->SetTextColor(179,179,179);
                $pdf->SetXY($size['width']/3,$size['height']/3);
                $pdf->Write(0,$worter_data);
                $pdf->SetXY($size['width']/2,$size['height']/2);
                $pdf->Write(0,$worter_data);
                $pdf->SetXY($size['width']/1.2,$size['height']/1.2);
                $pdf->Write(0,$worter_data);
            }
            $pdf_url = app()->getRootPath() . 'public/convert/'. $get_extension2[0] . '.pdf';
            $pdf->Output('F',$pdf_url,false);
            $url = '/convert/'. $get_extension2[0] . '.pdf';
        }
        return view('viewfile', ['mode' => $mode, 'url' => $url]);
    }
    public function OfficeConverter($url, $out)
    {
        $sys = g_cache('office');
        exec("{$sys} --headless --invisible --convert-to pdf:writer_pdf_Export {$url} --outdir {$out} 2>&1", $res, $rc);
        if ($rc != 0) {
            echo '<h2>{{{(>_<)}}} 转换出错~</h2>';
            return false;
        }
        return true;
    }
    /**
     * 文件上传
     */
    public function eupload()
    {
        $ret = (new Upload())->up(0);
        if ($ret){
            $msg['errno'] = 0;
            $msg['data'] = [[
                'url'=>'/'.$ret['src'],
                'alt'=>'',
                'href'=>'',
            ]];
            return json($msg);
        }else{
            $msg['errno'] = 1;
            $msg['data'] = $ret;
            $msg['msg'] = "上传出错";
            return json($msg);
        }
    }

}
