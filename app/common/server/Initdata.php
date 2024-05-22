<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;

class Initdata
{
	public function down($id,$type='1'){
		if($id=='sys_user'||$id=='sys_role'){
			$sys_tpl = [
				'sys_user'=>['账号[username]','真实姓名[realname]', '密码[password 明文]', '手机号[tel]', '邮箱[mail]', '角色id[role]', '状态[status1:正常,0:禁用]', '备注[remark]', '层级id[sass_id]'],
				'sys_role'=>['角色名[name]', '上级[pid]', '状态[status2:正常,1:退回,0:保存]','备注[remark]']
			];
			$header =$sys_tpl[$id];
			$csvname ='[系统内置]'.$id;
		}else{
			$sfdp_ver_id = Db::name('sfdp_design_ver')->where('sid',$id)->where('status',1)->find();
			$header_data = Db::name('sfdp_field')->where('sid',$sfdp_ver_id['id'])->where('field','not in','id,create_time,update_time')->column('field,name_type,name');
			$header =[];
			foreach($header_data as $v){
				$header[] = $v['name'].'['.$v["field"].']';
			}
			$csvname =$sfdp_ver_id['s_name'].'['.$sfdp_ver_id["s_db"].']';
		}
        if($type==1){
            return $this->export($csvname,$header);
        }else{
            return ['sid'=>$id,'db_table'=>$sfdp_ver_id['s_db'],'name'=>$csvname,'data'=>implode(',',$header)];
        }
	}
    /**
     * 初始化方法
     * @param $type 导入数据类型
     * @param $data 导入数据
     * @return array
     */
    public function Init($type,$data)
    {
        if(count($data)<=0){
            return ['code'=>1,'msg'=>'未找到导入数据，请确认！'];
        }
        if($type=='sys_user'||$type=='sys_role'){
            return $this->$type($data);
        }else{
            return $this->sfdp_data($type,$data);
        }
    }

    /**
     * Sfdp 构建的业务统一导入数据
     * @param $type 导入的Sid
     * @param $data 导入数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected  function sfdp_data($type,$data){
        $sfdp_ver_id = Db::name('sfdp_design_ver')->where('sid',$type)->where('status',1)->find();
        $header_data = Db::name('sfdp_field')->where('sid',$sfdp_ver_id['id'])->where('field','not in','id,create_time,update_time')->column('field,name_type,name');
        $header =[];
        foreach($header_data as $v){
            $header[] = $v["field"];
        }
        if(count($data)>0 && count($data[1])==count($header)){
            $dataArray = [];
            foreach($data as $k=>$v){
                foreach($header as $k2=>$v2){
                    $dataArray[$k][$v2] = $v[$k2];
                }
                $dataArray[$k]['create_time'] = time();
                $dataArray[$k]['update_time'] = time();
            }
        }else{
            return ['code'=>1,'msg'=>'数据列不匹配，请勿修改模板'];
        }
        try{
            $ret = Db::name($sfdp_ver_id["s_db"])->insertAll($dataArray);
        }catch(\Exception $e){
            return ['code'=>-1,'msg'=>$e->getMessage()];
        }
        if($ret == count($data)){
            return ['code'=>0,'msg'=>'导入成功一共导入【'.$ret.'】！'];
        }else{
            return ['code'=>1,'msg'=>'导入失败一共导入【'.$ret.'】！'];
        }
    }

    /**
     * 角色数据初始化
     * @param $data 导入的数据
     * @return array
     */
    protected  function sys_role($data){
        $sys_user = ['name','pid','status','remark'];
        if(count($data)>0 && count($data[1])==count($sys_user)){
            $dataArray = [];
            foreach($data as $k=>$v){
                foreach($sys_user as $k2=>$v2){
                        $dataArray[$k][$v2] = $v[$k2];
                }
                $dataArray[$k]['update_time'] = time();
            }
        }else{
            return ['code'=>1,'msg'=>'数据列不匹配，请勿修改模板'];
        }
        $ret = Db::name('SoftRole')->insertAll($dataArray);
        if($ret == count($data)){
            return ['code'=>0,'msg'=>'导入成功一共导入【'.$ret.'】！'];
        }else{
            return ['code'=>1,'msg'=>'导入失败一共导入【'.$ret.'】！'];
        }
    }

    /**
     * 用户初始化
     * @param $data 导入的数据
     * @return array
     */
    protected  function sys_user($data){
        $sys_user = ['username','realname','password','tel','mail','role','status','remark','sass_id'];
        if(count($data)>0 && count($data[1])==count($sys_user)){
            $dataArray = [];
            foreach($data as $k=>$v){
                foreach($sys_user as $k2=>$v2){
                    if($v2=='password'){
                        $dataArray[$k][$v2] =  password_hash($v[$k2], PASSWORD_BCRYPT, ['cost' => 12]);
                    }else{
                        $dataArray[$k][$v2] = $v[$k2];
                    }

                }
                $dataArray[$k]['add_time'] = time();
            }
        }else{
            return ['code'=>1,'msg'=>'数据列不匹配，请勿修改模板'];
        }
        $ret = Db::name('SoftUser')->insertAll($dataArray);
        if($ret == count($data)){
            foreach ($dataArray as $k=>$v){
                $role['user_id'] = Db::name('SoftUser')->where('username',$v['username'])->value('id');
                $role['role_id'] = $v['role'];
                Db::name('softRoleUser')->insertGetId($role);
            }
            return ['code'=>0,'msg'=>'导入成功一共导入【'.$ret.'】！'];
        }else{
            return ['code'=>1,'msg'=>'导入失败一共导入【'.$ret.'】！'];
        }
    }
	static function getCsvData($path){
		$flag = false;
		//检测文件是否存在
		if($flag === false){
			if(!file_exists($path)){
				$flag = true;
			}
		}
		//检测文件格式
		if($flag === false){
			$ext = substr(strrchr($path, '.'), 1);
			if($ext != 'csv'){
				$flag = true;
			}
		}
		//读取文件
		if($flag == false){
			$row = 0;
			$handle = fopen($path,'r');
			$dataArray = array();
			while($data = fgetcsv($handle)){
				for($i=0;$i<count($data);$i++){
					if($row == 0){
						break;
					}
					//组建数据
					$dataArray[$row][$i] = mb_convert_encoding($data[$i], "UTF-8", "GBK");
				}
				$row++;
			}
		}
		return $dataArray;
	}
	/**
	 * @param $csvname 导出的 csv文件
	 * @param $header  头部信息
	 * @param $data
	 */
	protected function export($csvname,$header=[],$data=[])
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' .  self::Gbk($csvname) . '.csv"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$fp = fopen('php://output', 'a');
		mb_convert_variables('GBK', 'UTF-8', $header);
		fputcsv($fp, $header);
		foreach ($data as $row) {
			mb_convert_variables('GBK', 'UTF-8', $row);
			fputcsv($fp, $row);
			unset($row);
		}
		fclose($fp);
		exit;
	}
	/**
	 * @param $data string 转换数组
	 * @return false|string
	 */
	static function Gbk($data)
	{
		return iconv('utf-8', 'GBK', $data);
	}


}