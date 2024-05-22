<?php
/**
 *+------------------
 * 方块报表入口
 *+------------------
 */
declare (strict_types=1);

namespace Fkreport;

use Fkreport\adaptive\Common;
use Fkreport\adaptive\Data;
use Fkreport\adaptive\Fun;
use Fkreport\fun\unit;
use Fkreport\service\Control;

define('Fk_URL', realpath(dirname(__FILE__)));

define('fk_Ver', '1.0');

class Api
{
    protected $tpl;//模板路径

    public function __construct()
    {
        $ginfo = unit::getuserinfo();
        if($ginfo==-1){
            echo 'Access Error!';exit;
        }

        $this->tpl = Fk_URL . '/view/';
    }

    /**
     * 方块报表统一API接口
     * @param string $act 调用接口方法
     * @param string $id 传递id
     * @return \think\response\View|void
     */
    public function Api($act = 'index', $id = '')
    {
        if ($act == 'index') {
            return view($this->tpl . 'index.html', ['data' => Data::all()]);
        }
        if ($act == 'view' || $act == 'desc') {
            $row = Data::find($id);
            if($row['data']['type']==1){
                return view($this->tpl . $act.'2.html', ['id'=>$id,'data'=>$row,'url_api'=>unit::gconfig('url_api')]);
            }
            return view($this->tpl . $act.'.html', ['id'=>$id,'data'=>$row,'url_api'=>unit::gconfig('url_api')]);
        }
        if ($act == 'viewdata') {
            $row = Data::find($id);
            $data = Fun::getFun($row['data']['fun'],0);//获取元素数据信息
            $json = json_decode($row['data']['data'],true);

            foreach ($json as $key=>$item){
                foreach($item['celldata'] as $k=>$v) {
                    $r = $v['r'];
                    if ( isset($v['v']['v']) && $v['v']['v'] != 0 && strpos($v['v']['v'], '#{') !== false) {
                        $field = explode("','",ltrim(rtrim($v['v']['v'],"'}"),"#{'"));
                        if( array_key_exists($field[0], $data) ){  //检测数组中是否存在该键
                            foreach($data[$field[0]] as $k2=>$v2){
                                $v3 = $v;
                                $v3['r']=$r;
                                $v3['v']['v']=$v2;
                                array_push($json[$key]['celldata'],$v3);
                                unset($v3);
                                $r = $r+1;
                            }
                        }
                    }
                    $xx = $v['r'];
                    if (isset($v['v']['f'])) {
                        $nexf = $v['v']['f'];
                        for ($i = 1; $i <= count($data[$field[0]]); $i++) {
                            $v4 = $v;
                            if($xx!= $v4['r']){
                                $v4['r'] = $xx;
                                $v4['f'] = $nexf;

                                $v4['v']['f'] = $nexf;
                                array_push($json[$key]['celldata'], $v4);
                                array_push($json[$key]['calcChain'], ['index'=>'sheet_01','r'=>$xx,'c'=>$v['c']]);

                            }

                            unset($v4);
                            $xx = $xx + 1;
                            $nexf= $this->incadd($nexf);
                        }
                    }

                }
                unset($json[$key]['data']);
            }
            //$json[0]['config']['borderInfo'][0]['range'][0]['row'] = [0,count($data[$field[0]])];
            echo json_encode($json);
        }

        if ($act == 'add') {
            if (unit::is_post()) {
                $data = input('post.');
                return Control::api('save', 0, $data);
            } else {
                return view($this->tpl . 'add.html',['url'=>'/gadmin/fk/api?act=add']);
            }
        }
        if ($act == 'edit') {
            if (unit::is_post()) {
                $data = input('post.');
                return Control::api('editsave', $id, $data);
            } else {
                $row = Data::find($id);
                return view($this->tpl . 'add.html',['data'=>$row['data'],'url'=>'/gadmin/fk/api?act=edit&id='.$id]);
            }
        }
        if ($act == 'save') {
            if (unit::is_post()) {
                $data = input('post.');
                return Control::api('save', $id, $data);
            } else {
                return view($this->tpl . 'add.html');
            }
        }
         if ($act == 'deldesc') {
            return Control::api('deldesc', $id);
        }
        
    }
    public function incadd($strs) {
        return preg_replace_callback('/\d+/', function($m){
           return $m[0]+1;
        }, $strs);
    }
    /**
     * 函数接口
     * @param string $act
     * @return Json
     */
    public function fun($act = 'index')
    {
        if ($act == 'index') {
            return view($this->tpl . 'fun.html', ['data' => Fun::all()]);
        }
        if ($act == 'add') {
            $data = input('post.');
            return Fun::save($data);
        }
        if ($act == 'sapi') {
            $data = input('post.');
            return Fun::sapi($data);
        }
    }

    /**
     * 资源接口数据
     * @param string $act
     * @return Json
     */
    public function data($act = 'list')
    {
        if ($act == 'list') {
            if (unit::is_post()) {
                $data = Common::file(input('limit'),input('page'));
                return json($data);
            } else {
                return view($this->tpl . 'files.html',['upload'=>unit::gconfig('upload_file')]);
            }
        }
    }
}
	