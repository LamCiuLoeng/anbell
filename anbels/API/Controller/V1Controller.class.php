<?php
namespace API\Controller;
use Think\Controller;

// +----------------------------------------------------------------------
// | version 1.0
// +----------------------------------------------------------------------
class V1Controller extends Controller {	
	public function test(){
		$aa = array('aa'=> 'bb', 'cc' => 'dd');
		dump($this->_f($aa, array('aa','cc')));
		
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
			$result['user'] = $this->_f($result['user'],array('id','system_no','name','gender','last_login_time'));
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
	
	private function _f($obj,$fields){
		$result = array();
		foreach ($fields as $f) {
			if(array_key_exists($f,$obj)){
				$result[$f] = $obj[$f];
			}
		}
		return $result;
		
	}
	
	private function _flist($list,$fields){
		$result = array();
		for ($i=0; $i < count($list); $i++) {
			array_push($result,$this->_f($list[$i],$fields));
		}
		return $result;
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
			try{
				if($action == 'test'){ $this->ajaxReturn(array('flag' => FLAG_OK , 'msg' => MSG_OK)); }
				if($action == 'get_user_info'){ $this->get_user_info(); }
				if($action == 'get_plan_by_user'){ $this->get_plan_by_user(); }
				if($action == 'get_plan_by_class'){ $this->get_plan_by_class(); }
				if($action == 'get_questions'){ $this->get_questions(); }
				if($action == 'save_user_data'){ $this->save_user_data(); }
			}catch(Exception $e){
				$this->ajaxReturn(array('flag' => FLAG_UNKNOWN_ERROR, 'msg' => MSG_UNKNOWN_ERROR));
			}
				
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
		$User = M("AuthUser");
		$user = $User->where(array('id' => intval($params['data']['user_id']), 'active' => 0))->find();
		if(!$user || is_null($user)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
		}else{
			$data = $this->_f($user,array('id','system_no','name','gender','last_login_time'));
			$this->ajaxReturn(array('flag' => FLAG_OK , 'data' => $data));
		}
	}
	
	// +----------------------------------------------------------------------
	// | 利用用户ID获取教堂计划
	// +----------------------------------------------------------------------
	private function get_plan_by_user(){
		$params = $this->_required('user_id');
		$ClassUser = M('LogicClassUser');		
		$class_id = $ClassUser->where(array('user_id' => intval($params['data']['user_id'])))->getField('class_id');
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
		$c = $class->where(array('id' => intval($class_id)))->find();
		if(!$c || is_null($c)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
		}else{
			$Plan = M('LogicPlan');
			$plan = $Plan->where(array('school_id' => intval($c['school_id']),
									  'grade' => intval($c['grade']),
									  'active' => 0))->find();
				
			if(!$plan || is_null($plan)){
				$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
			}else{
				$plan = $this->_f($plan,array('id','name','school_id','grade','desc'));
				$pid = $plan['id'];
				$Courseware = M('LogicPanCourseware');
				$cs = $Courseware->where(array('plan_id' => $pid ,'active' => 0))->select();
				if(!$cs || is_null($cs)){
					$plan['courseware'] = array();
				}else{
					$plan['courseware'] = $this->_flist($cs, array('plan_id','category_id','obj_id'));
				}
				$Game = M('LogicPanGame');
				$gs = $Game->where(array('plan_id'=> $pid,'active' => 0 ))->select();
				// $this->ajaxReturn($gs);
				if(!$gs || is_null($gs)){
					$plan['game'] = array();
				}else{
					$plan['game'] = $this->_flist($gs, array('plan_id','category_id','obj_id'));
				}				
				$this->ajaxReturn(array('flag' => FLAG_OK ,'data' => $plan));				
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
		$LogicStudyLog = M('LogicStudyLog');
		if($params['data']['data_type'] == 'C' || $params['data']['data_type'] == 'P'){  //课件或者课程的数据
		    $type = $params['data']['data_type'];
			$condition = array('active' => 0 , 'user_id' => $params['data']['user_id'] , 
							    'type' => $type , 'refer_id' => $params['data']['obj_id'] ,
							    );
			$before = $LogicStudyLog->where($condition)->find();
			if(!$before || is_null($before)){ //no play the game before
				$dto = mydto();
				$dto['user_id'] = $params['data']['user_id'];
				$dto['refer_id'] = $params['data']['obj_id'];
				$dto['type'] = $type;
				$dto['complete_time'] = mynow();
				$LogicStudyLog->data($dto)->add();
			}else{
				$update['complete_time'] = mynow();
				$LogicStudyLog->where($condition)->data($update)->save();
			}
			$this->ajaxReturn(array('flag' => FLAG_OK ,'msg' => MSG_OK));
		
		}elseif($params['data']['data_type'] == 'G'){  //游戏的数据
			$condition = array('active' => 0 , 'user_id' => $params['data']['user_id'] , 
							    'type' => 'G' , 'refer_id' => $params['data']['obj_id'] ,
							    );
			//check if the user do the game befoe
			$before = $LogicStudyLog->where($condition)->find();
			if(!$before || is_null($before)){ //no play the game before
				$dto = mydto();
				$dto['user_id'] = $params['data']['user_id'];
				$dto['refer_id'] = $params['data']['obj_id'];
				$dto['type'] = 'G';
				$dto['complete_time'] = mynow();
				$dto['score'] = I('score',null);
				$LogicStudyLog->create($dto);
				$LogicStudyLog->add();
			}else{ //update the exist record
				$update['score'] = I('score',null);
				$update['complete_time'] = mynow();
				$LogicStudyLog->where($condition)->data($update)->save();
			}
			$this->ajaxReturn(array('flag' => FLAG_OK ,'msg' => MSG_OK));
		}else{
			$this->ajaxReturn(array('flag' => FLAG_NO_ACTION_TYPE ,'msg' => MSG_NO_ACTION_TYPE));
		}
	}

	
	// +----------------------------------------------------------------------
	// | 随机获取相关分类的问题
	// +----------------------------------------------------------------------
	private function get_questions(){
		$params = $this->_required('category_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' => FLAG_NOT_ALL_REQUIRED ,'msg' => MSG_NOT_ALL_REQUIRED));
		}
		$total = I('total',"5");
		$Q = M('MasterQuestion');
		$q = $Q->where(array('active' => 0 , 'category_id' => intval($params['data']['category_id'])))->order('RAND()')->limit(intval($total))->select();

		if(!$q || is_null($q)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
		}else{
			$q = $this->_flist($q, array('id','content','correct_answer',
									'answer01','answer01_content','answer02','answer02_content','answer03','answer03_content',
									'answer04','answer04_content','answer05','answer05_content','answer06','answer06_content',
									'answer07','answer07_content','answer08','answer08_content','answer09','answer09_content',
									'answer10','answer10_content'
			));
			$this->ajaxReturn(array('flag' => FLAG_OK, 'data' => $q));
		}
	}

}
    