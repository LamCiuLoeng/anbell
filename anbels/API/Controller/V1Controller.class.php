<?php
namespace API\Controller;
use Think\Controller;

// +----------------------------------------------------------------------
// | version 1.0
// +----------------------------------------------------------------------
class V1Controller extends Controller {	
	public function test(){
		
			
	}

	public function login(){
		$name = I('name',null);
		$pw = I('password',null);
		$result = login_check($name,$pw);
		if($result['flag'] != 0 ){
			$this->ajaxReturn($result);
		}else{
			$result['apikey'] = generatekey();
			$this->ajaxReturn($result);
		}
	}

	
	private function _required(){
		$data = array();
		foreach(func_get_args() as $arg){
			$tmp = I($arg,null);
			if(!$tmp || is_null($tmp)){
				 return array('flag' => FALSE);
			}else{
				$data[$arg] = $tmp;
			}
		}
		return array('flag' => TRUE, 'data' => $data);
	}
	
	
	// +----------------------------------------------------------------------
	// | check the api key before run the every api function
	// +----------------------------------------------------------------------
	public function _before_action(){
		$key = I('apikey',null);
	}
		
	
	public function action()
	{
		$action = I('_q',NULL);
		if(!$action || is_null($action)){
			$this->ajaxReturn(array('flag' => -1 , 'msg' => '非法操作！'));
		}else{
			if($action == 'get_user_info'){ $this->get_user_info(); }
		}
	}
	
	
	
	// +----------------------------------------------------------------------
	// | 获取用户的基本信息
	// +----------------------------------------------------------------------
	private function get_user_info(){
		$params = $this->_required('user_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' => 0 ,'msg' => 'API参数不全！'));
		}
		
	}
	
	// +----------------------------------------------------------------------
	// | 利用用户ID获取教堂计划
	// +----------------------------------------------------------------------
	private function get_plan_by_user(){
		$params = $this->_required('user_id');
		
	}
	
	
	// +----------------------------------------------------------------------
	// | 利用班级ID获取教堂计划
	// +----------------------------------------------------------------------
	private function get_plan_by_class(){
		$params = $this->_required('class_id');
		$class = M('MasterClass');
		$c = $class->where(array('id' => $params['data']['class_id']))->find();
		if(!$c || is_null($c)){
			$this->ajaxReturn(array('flag' => 1 , 'msg' => '记录不存在！'));
		}else{
			$Plan = M('LogicPlan');
			$pan = $Plan->where(array('school_id' => $c['school_id'],
									  'grade' => $c['grade'],
									  'active' => 0,
			))->find();
			if(!$plan || is_null($plan)){
				$this->ajaxReturn(array('flag' => 1 , 'msg' => '记录不存在！'));
			}else{
				$pid = $plan['id'];
				$Courseware = M('LogicPanCourseware');
				$plan['courseware'] = $Courseware->where(array('plan_id' => $pid))->select();
				
				$Game = M('LogicPanGame');
				$plan['game'] = $Game->where(array('plan_id'=> $pid ))->select();
				
				$this->ajaxReturn(array('flag' => 0 ,'data' => $plan ));
			}
		}
	}
	
	// +----------------------------------------------------------------------
	// | 记录游戏相关的数据
	// +----------------------------------------------------------------------
	private function save_courseware_data(){
		
	}
	
	// +----------------------------------------------------------------------
	// | 记录游戏相关的数据
	// +----------------------------------------------------------------------
	private function save_game_data(){
		
	}
		
}
    