<?php
namespace API\Controller;
use Think\Controller;

// +----------------------------------------------------------------------
// | version 1.0
// +----------------------------------------------------------------------
class V1Controller extends Controller {	
	public function test(){
		echo KK;
			
	}

	public function login(){
		$system_no = I('system_no',null);
		$pw = I('password',null);
		$result = login_check($system_no,$pw);
		if($result['flag'] != FLAG_OK ){
			$this->ajaxReturn($result);
		}else{
			$result['user_key'] = generatekey();
			$CurrentUser =  M('SystemCurrentUser');			
			$CurrentUser->data(array('user_key' => $result['user_key'],'time_stamp' => mynow() ))->add();
			$this->ajaxReturn($result);
		}
	}

	
	private function _required(){
		$data = array();
		foreach(func_get_args() as $arg){
			$tmp = I($arg,null);
			if(!$tmp || is_null($tmp)){
				 return array('flag' => FALSE ,'msg' => MSG_NOT_ALL_REQUIRED);
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
		$m['user_key'] = I('user_key',null);
		$CurrentUser = M('SystemCurrentUser');
		$u = $CurrentUser->where($m)->find();
		if(!u || is_null($u)){
			 $this->ajaxReturn(array('flag' => FLAG_NOT_ALLOW , 'msg' => MSG_NOT_ALLOW)); 
		}
		else{
			$update['time_stamp'] = mynow();
			$CurrentUser->where($m)->save($update);
		}
	}
		
	
	public function action()
	{
		$action = I('_q',NULL);
		if(!$action || is_null($action)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_ALLOW , 'msg' => MSG_NOT_ALLOW));
		}else{
			if($action == 'test'){ $this->ajaxReturn(array('flag' => FLAG_OK , 'msg' => MSG_OK)); }
			if($action == 'get_user_info'){ $this->get_user_info(); }
			if($action == 'get_plan_by_user'){ $this->get_plan_by_user(); }
			if($action == 'get_plan_by_class'){ $this->get_plan_by_class(); }
			if($action == 'save_user_data'){ $this->save_user_data(); }
		}
	}
	
	
	
	// +----------------------------------------------------------------------
	// | 获取用户的基本信息
	// +----------------------------------------------------------------------
	private function get_user_info(){
		$params = $this->_required('user_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' =>  FLAG_NOT_ALL_REQUIRED,'msg' => MSG_NOT_ALL_REQUIRED));
		}
		
	}
	
	// +----------------------------------------------------------------------
	// | 利用用户ID获取教堂计划
	// +----------------------------------------------------------------------
	private function get_plan_by_user(){
		$params = $this->_required('user_id');
		$ClassUser = M('LogicClassUser');
		$class_id = $ClassUser->where(array('user_id' => $params['user_id']))->getField('class_id');
		if(!$class_id || is_null($class_id)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
		}else{
			$this->_get_plan_detail($class_id);
		}
	}
	
	
	// +----------------------------------------------------------------------
	// | 利用班级ID获取教堂计划
	// +----------------------------------------------------------------------
	private function get_plan_by_class(){
		$params = $this->_required('class_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' =>  FLAG_NOT_ALL_REQUIRED, 'msg' => MSG_NOT_ALL_REQUIRED));
		}
		$this->_get_plan_detail($params['data']['class_id']);
	}
	

	
	private function _get_plan_detail($class_id){
		$class = M('MasterClass');
		$c = $class->where(array('id' => $class_id))->find();
		if(!$c || is_null($c)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
		}else{
			$Plan = M('LogicPlan');
			$pan = $Plan->where(array('school_id' => $c['school_id'],
									  'grade' => $c['grade'],
									  'active' => 0,
			))->find();
			if(!$plan || is_null($plan)){
				$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
			}else{
				$pid = $plan['id'];
				$Courseware = M('LogicPanCourseware');
				$plan['courseware'] = $Courseware->where(array('plan_id' => $pid))->select();
				
				$Game = M('LogicPanGame');
				$plan['game'] = $Game->where(array('plan_id'=> $pid ))->select();
				
				$this->ajaxReturn(array('flag' => FLAG_OK ,'data' => $plan ));
			}
		}
	}
	
	
	// +----------------------------------------------------------------------
	// | 记录用户课件或者游戏相关的数据
	// +----------------------------------------------------------------------
	private function save_user_data(){
		$params = $this->_required('user_id','data_type','obj_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' => FLAG_NOT_ALL_REQUIRED ,'msg' => MSG_NOT_ALL_REQUIRED));
		}
		
		if($params['data_type'] == 'C'){  //课件的数据			
			$LogicStudyLog = M('LogicStudyLog');
			$dto = mydto();
			$dto['user_id'] = $params['user_id'];
			$dto['refer_id'] = $params['obj_id'];
			$dto['type'] = 'C';
			$dto['complete_time'] = mynow();
			M('LogicStudyLog')->create($dto);
			$this->ajaxReturn(array('flag' => FLAG_OK ,'msg' => MSG_OK));
		}elseif($params['data_type'] == 'G'){  //游戏的数据
			$LogicStudyLog = M('LogicStudyLog');
			$dto = mydto();
			$dto['user_id'] = $params['user_id'];
			$dto['refer_id'] = $params['obj_id'];
			$dto['type'] = 'G';
			$dto['complete_time'] = mynow();
			$dto['score'] = I('score',null);
			M('LogicStudyLog')->create($dto);
			$this->ajaxReturn(array('flag' => FLAG_OK ,'msg' => MSG_OK));
		}else{
			$this->ajaxReturn(array('flag' => FLAG_NO_ACTION_TYPE ,'msg' => MSG_NO_ACTION_TYPE));
		}
	}
}
    