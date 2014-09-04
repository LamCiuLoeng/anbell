<?php
namespace API\Controller;
use Think\Controller;

// +----------------------------------------------------------------------
// | version 1.0
// +----------------------------------------------------------------------
class V1Controller extends Controller {	
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
				if($action == 'get_user_info'){ $this->get_user_info(); }
                if($action == 'get_course_by_plan'){ $this->get_course_by_plan(); }
                if($action == 'get_course_by_user'){ $this->get_course_by_user(); }
				if($action == 'get_course_info'){ $this->get_course_info(); }
				if($action == 'get_questions'){ $this->get_questions(); }
				if($action == 'save_user_data'){ $this->save_user_data(); }
				if($action == 'get_study_log'){ $this->get_study_log(); }
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
			
            $grade = M()->query("SELECT s.id as school_id , c.grade as grade  ,c.id as class_id
							from anbels_master_class c , anbels_logic_class_user cu , anbels_master_school s
							where c.school_id = s.id and c.id = cu.class_id and cu.user_id = ".$user['id']);
			if($grade && is_array($grade)){
			    $data['grade'] = $grade[0]['grade'];
				$data['school_id'] = $grade[0]['school_id'];
				$data['class_id'] = $grade[0]['class_id'];
				
				$plan = M()->query("SELECT p.id as plan_id
							FROM anbels_master_school s, anbels_logic_plan p
							WHERE s.active=0 and p.active=0 and s.id = p.school_id and s.id = ".$data['school_id']);
				
				if($plan && is_array($plan)){
					$data['plan_id'] = $plan[0]['plan_id'];
				}else{
					$data['plan_id'] = null;
				}
			}else{
			    $data['grade'] = null;
				$data['school_id'] = null;
				$data['class_id'] = null;
				$data['plan_id'] = null;
			}
			
			$this->ajaxReturn(array('flag' => FLAG_OK , 'data' => $data));
		}
	}
	
	// +----------------------------------------------------------------------
	// | 利用用户ID获取教学计划
	// +----------------------------------------------------------------------
	/*
	private function get_plan_by_user(){
		$params = $this->_required('user_id');
		$ClassUser = M('LogicClassUser');
        $result = $ClassUser->join('anbels_master_class on anbels_master_class.id = anbels_logic_class_user.class_id')
                  ->where(array('anbels_logic_class_user.user_id' => intval($params['data']['user_id'])))
                  ->getField('anbels_master_class.school_id,anbels_master_class.grade');
        
        if(!$result || is_null($result)){
			$this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
        }else{
			$this->_get_plan_detail($result['school_id'],$result['grade']);
        }
	}
	*/
    
    // +----------------------------------------------------------------------
    // | 利用教学计划ID获取所有的课程
    // +----------------------------------------------------------------------
    private function get_course_by_plan()
    {
        $params = $this->_required('plan_id');
        $LogicPlanCourse = M('LogicPlanCourse');
        $conditions = array('anbels_master_course.active' => 0,
                            'anbels_logic_plan_course.active' => 0,
                            'anbels_master_courseware.active' => 0,
                            'anbels_logic_plan_course.plan_id'=>intval($params['plan_id']));
        $grade = I('grade',null);
        if($grade){
            $conditions['anbels_logic_plan_course.grade'] = intval($grade);
        }
        $cs = $LogicPlanCourse
        ->join("anbels_master_course on anbels_master_course.id = anbels_logic_plan_course.course_id")
        ->join("anbels_master_courseware on anbels_master_courseware.id = anbels_logic_plan_course.course_id")
        ->field("anbels_logic_plan_course.grade,anbels_master_course.name as course_name,anbels_logic_plan_course.course_id as course_id,
                anbels_master_courseware.id as courseware_id,anbels_master_courseware.name as courseware_name,
                anbels_master_courseware.url as courseware_url")
        ->order("anbels_logic_plan_course.grade,anbels_master_course.id")
        ->where($conditions)
        ->select();
        
        $result = array();
        foreach ($cs as $c) {
            if(array_key_exists($c['grade'],$result)){
                if(array_key_exists($c['course_id'],$result['grade'])){
                    array_push(array("courseware_id" => $c['courseware_id'],
                                      "courseware_name" => $c['courseware_name'],
                                      "courseware_url" => $c['courseware_url']),$result['grade'][$c['course_id']]);
                }else{
                    $result['grade'][$c['course_id']] = array(
                                              'name' => $c['course_name'],
                                              'coursewares' =>array(
                                                                array("courseware_id" => $c['courseware_id'],
                                                                      "courseware_name" => $c['courseware_name'],
                                                                      "courseware_url" => $c['courseware_url']),
                                                              )
                                              );
                }
            }else{
                $result[$c['grade']] = array(
                                        $c['course_id'] =>array(
                                              'name' => $c['course_name'],
                                              'coursewares' =>array(
                                                                array("courseware_id" => $c['courseware_id'],
                                                                      "courseware_name" => $c['courseware_name'],
                                                                      "courseware_url" => $c['courseware_url']),
                                                              )
                                              )
                );
            }
        }
        $this->ajaxReturn(array('flag' => FLAG_OK , 'data' => $result));
    }
    
    
    // +----------------------------------------------------------------------
    // | 获取用户的所有课程
    // +----------------------------------------------------------------------
    private function get_course_by_user()
    {
        $params = $this->_required('user_id');
        if(!$params['flag']){
            $this->ajaxReturn(array('flag' =>  FLAG_NOT_ALL_REQUIRED, 'msg' => MSG_NOT_ALL_REQUIRED));
        }
        
        $school = getUserSchool($params['data']['user_id']);
        $Model = M();
             
        $rows = $Model->query('SELECT cr.id as course_id,cr.name as course_name,cr.desc as descripiton,
                              crw.id as courseware_id, 
                              crw.name as courseware_name,
                              crw.url as courseware_url
                       FROM anbels_logic_plan p, anbels_logic_plan_course pcr ,anbels_master_course cr,anbels_master_courseware crw
                       WHERE 
                            cr.active = 0 and crw.active = 0 and p.active = 0 and pcr.active = 0 and
                            
                            p.id = pcr.plan_id and
                            pcr.course_id = cr.id and
                            crw.course_id = cr.id and
                            p.school_id ='.$school['id']);        
        $result = array();
        foreach ($rows as $row) {
            if(array_key_exists($row['course_id'], $result)){
                array_push($result[$row['course_id']]['coursewares'],array('id' => $row['courseware_id'],'name' => $row['courseware_name'], 'url' => $row['courseware_url']));
            }else{
               $result[$row['course_id']] = array(
                                           'id' => $row['course_id'],
                                           'name' => $row['course_name'],
                                           'desc' => $row['descripiton'],
                                           'coursewares' => array( array('id' => $row['courseware_id'] , 'name' => $row['courseware_name'] , 'url' => $row['courseware_url']) )
                                           );
            }
        }
        $this->ajaxReturn(array('flag' => FLAG_OK , 'data' => $result));
    }
        
        
    // +----------------------------------------------------------------------
    // | 获取某一个课程的详细信息
    // +----------------------------------------------------------------------
    private function get_course_info()
    {
        $params = $this->_required('course_id');
        if(!$params['flag']){
            $this->ajaxReturn(array('flag' =>  FLAG_NOT_ALL_REQUIRED, 'msg' => MSG_NOT_ALL_REQUIRED));
        }  
        $Model = M();
        $q = $Model->query("SELECT id,name,`desc`
                            FROM anbels_master_course
                            WHERE id = ".$params['data']['course_id']);
        if(!$q || is_null($q)){
            $this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
        }
        
        if(is_array($q)){
            $q = $q[0];
        }

        $result['id'] = $q['id'];
        $result['desc'] = $q['desc'];
        $result['name'] = $q['name'];
        
        $crws = $Model->query("SELECT id,name,`desc`,url
                       FROM anbels_master_courseware
                       WHERE active = 0 and course_id = ".$q['id']);
        if(!crws || is_null($crws)){
            $result['coursewares'] = array();
        }else{
            $result['coursewares'] = $crws;
        }
        
        $this->ajaxReturn(array('flag' => FLAG_OK , 'data' => $result));
    }
    
    
	
	// +----------------------------------------------------------------------
	// | 利用班级ID获取教堂计划
	// +----------------------------------------------------------------------
	/*
	private function get_plan_by_class(){
		$params = $this->_required('class_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' =>  FLAG_NOT_ALL_REQUIRED, 'msg' => MSG_NOT_ALL_REQUIRED));
		}
        $Class = M('MasterClass');
        $class = $Class->where(array('active' => 0 , 'id' =>$params['data']['class_id']))->find();
        if(!$class || is_null($class)){
            $this->ajaxReturn(array('flag' => FLAG_NOT_EXIST , 'msg' => MSG_NOT_EXIST));
        }else{
    		$this->_get_plan_detail($class['school_id'],$class['grade']);
            
        }
	}
    */ 
	

	/*
	private function _get_plan_detail($school_id,$grade){
		$LogicPlanCourse = M('LogicPlanCourse');
        $cs = $LogicPlanCourse
            ->join("anbels_logic_plan on anbels_logic_plan.id = anbels_logic_plan_course.plan_id")
            ->join("anbels_master_course on anbels_master_course.id = anbels_logic_plan_course.course_id")
            ->where("")
            ->order("")
            ->field("");
        
		if(!$cs || is_null($cs)){
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
			
				$this->ajaxReturn(array('flag' => FLAG_OK ,'data' => $plan));				
			}
		}
	}
	*/
	
	// +----------------------------------------------------------------------
	// | 记录用户课件或者游戏相关的数据
	// +----------------------------------------------------------------------
	private function save_user_data(){
		$params = $this->_required('user_id','data_type','begin_or_end','obj_id','school_id','grade','class_id','plan_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' => FLAG_NOT_ALL_REQUIRED ,'msg' => MSG_NOT_ALL_REQUIRED));
        }
        
        if($params['data']['begin_or_end'] != 'BEGIN' && $params['data']['begin_or_end'] != 'END'){
            $this->ajaxReturn(array('flag' => FLAG_NO_ACTION_TYPE ,'msg' => MSG_NO_ACTION_TYPE));
        }

        if($params['data']['data_type'] == 'C' || $params['data']['data_type'] == 'G' || $params['data']['data_type'] == 'P'){
            $type = $params['data']['data_type'];
            $condition = array('active' => 0 , 'user_id' => $params['data']['user_id'] , 
                                'type' => $type , 'refer_id' => $params['data']['obj_id'] ,
                                );
            $LogicStudyLog = M('LogicStudyLog');
            $before = $LogicStudyLog->where($condition)->find();
            
            if($params['data']['begin_or_end'] == 'BEGIN'){

                if(!$before || is_null($before)){ //no play the game before
                    $dto = mydto();
                    $dto['user_id'] = $params['data']['user_id'];
                    $dto['refer_id'] = $params['data']['obj_id'];
					$dto['school_id'] = $params['data']['school_id'];
					$dto['grade'] = $params['data']['grade'];
					$dto['class_id'] = $params['data']['class_id'];
					$dto['plan_id'] = $params['data']['plan_id'];	
                    $dto['type'] = $type;
                    $dto['start_time'] = mynow();
                    if($params['data']['data_type'] == 'C'){
                        $Q = M('MasterCourseware');
                        $q = $Q->where(array('id'=>intval($params['data']['obj_id'])))->find();
                        if($q){
                            $dto['refer_name'] = $q['name'];
                        }
                    }elseif($params['data']['data_type'] == 'P'){
                        $Q = M('MasterCourse');
                        $q = $Q->where(array('id'=>intval($params['data']['obj_id'])))->find();
                        if($q){
                            $dto['refer_name'] = $q['name'];
                        }
                    }else{
                        $dto['refer_name'] = '游戏';
                    }
                    $LogicStudyLog->data($dto)->add();
                }else{
                    $update['start_time'] = mynow();
                    $update['complete_time'] = null;
                    //$update['score'] = I('score',null);
                    $LogicStudyLog->where($condition)->data($update)->save();
                }
                $this->ajaxReturn(array('flag' => FLAG_OK ,'msg' => MSG_OK));
            }else{
                if($before){ //get the record before
                    $update['complete_time'] = mynow();
                    $score = I('score',null);
                    if($score){
                        $s = floatval($score);
                        if(is_null($before['score']) || $before['score'] < $s){
                            $update['score'] = $s;
                        }
                    }
                    $LogicStudyLog->where($condition)->data($update)->save();                            
                }
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
		$params = $this->_required('course_id');
		if(!$params['flag']){
			$this->ajaxReturn(array('flag' => FLAG_NOT_ALL_REQUIRED ,'msg' => MSG_NOT_ALL_REQUIRED));
		}
		$total = I('total',"5");
		$Q = M('MasterQuestion');
        $q = $Q 
           ->where(array('active' => 0 ,'course_id' => intval($params['data']['course_id']),))
           ->order('RAND()')->limit(intval($total))
           ->select();
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
    
    
    
    // +----------------------------------------------------------------------
    // | 获取用户的学习记录
    // +----------------------------------------------------------------------
    private function get_study_log()
    {
        $params = $this->_required('user_id');
        if(!$params['flag']){
            $this->ajaxReturn(array('flag' => FLAG_NOT_ALL_REQUIRED ,'msg' => MSG_NOT_ALL_REQUIRED));
        }
        
        $LogicStudyLog = M('LogicStudyLog');
        $ls = $LogicStudyLog->where(array('active' => 0 ,'user_id' => intval($params['data']['user_id'])))
        ->order("create_time")->select();
        $this->ajaxReturn(array('flag' => FLAG_OK, 'data' => $ls));
    }
}
    