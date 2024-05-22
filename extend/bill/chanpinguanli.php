<?php

namespace bill;

use think\facade\Db;
use tpflow\lib\unit;
/**
 *+------------------
 * chanpinguanli 工作流类
 *+------------------
 */
class chanpinguanli {
	
	protected $id; //对应单据编号
    protected $run_id; //运行中的流程id
    protected $userinfo; //用户信息
    protected $run_info; //获取流程信息
    protected $run_status; //获取流程状态 status = 0 流程中；1审核通过
    protected $bill_info; //读取流程单据信息

    public function  __construct($id,$run_id='',$data=''){
        $this->id =$id; //运行单据的编号
        $this->run_id =$run_id;//运行流程的主id
		$this->userinfo = unit::getuserinfo();//获取用户信息数组
		if($run_id!=''){
			$run_info = Db::name('wf_run')->find($run_id);
			$this->run_info = $run_info;//获取流程信息
			$this->run_status = $run_info['status'];//获取流程状态 status = 0 流程中；1审核通过
			$this->bill_info = Db::name($run_info['from_table'])->find($id);//读取流程单据信息
		}
    }
	/**
	*审批提交前
	*$action Start流程发起前 | 步骤运行前 ok 提交 back 回退 sing 会签  Send 发起
	*/
	public function before($action=""){
	echo 12311233;


	return ["code"=>0,"msg"=>"success"];
}

	/**
	 *审批完成后，日志记录前
	 *$action 操作状态 ok 提交 back 回退 sing 会签  Send 发起
	 */
	public function after($action=""){
   if($action=='ok'){
            $run = Db::name('wf_run')->find($this->run_id);
            if($run['status']==1){//判1断当前工作流是不是已经完成了
	Db::name('chanpinguanli')->update(['nums'=>100,'id'=>$this->id]);

	
            }
   }
  return ["code"=>0,"msg"=>"success"];
}

	/**
	*去审批事件
	*/
	public function cancel($action=""){



	return ["code"=>0,"msg"=>"success"];
}
}
?>