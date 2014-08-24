<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class UtilController extends BaseController {
    
    public function get_location_children()
    {
        $code = I('_c',null);
        if(!$code){
            $this->ajaxReturn(array('flag' => 1,'data' => null));
        }
        
        $Location = M('MasterLocation');
        $children = $Location->where(array('active' => 0 , 'parent_code' => $code))->order('name')->select();
        
        $School = M('MasterSchool');
        $school = $School->join('anbels_master_location on anbels_master_location.id = anbels_master_school.location_id')
        ->where('anbels_master_school.active = 0 and anbels_master_location.code="'.$code.'"')
        ->field('anbels_master_school.id,anbels_master_school.name')
        ->select();
        
        $this->ajaxReturn(array('flag' => 0 ,'children' => $children  ,'school' => $school));
    }
    
    
    public function get_class_by_school()
    {
        $id = I('_c',null);
        if(!$id || is_null($id)){
            $this->ajaxReturn(array('flag' => 1 , 'msg' => 'No ID provided!'));
        }
        
        $Class = M('MasterClass');
        $cs = $Class->where(array('active' => 0 , 'school_id' => intval($id)))->select();
        $this->ajaxReturn(array('flag' => 0 ,'data' => $cs));
    }
	
	public function ssq_handle()
	{
		$master_location = M("master_location"); // 实例化User对象
		$data = $master_location->select();
		$this->ajaxReturn($data);
	}
	
	public function request_school()
	{
		$master_school = M("master_school"); // 实例化User对象
		$data = $master_school->join('left join anbels_master_location on anbels_master_location.id = anbels_master_school.location_id')
		->where('anbels_master_school.active=0')
		->field('anbels_master_school.id,anbels_master_school.name,
				anbels_master_location.code')
		->select();
		$this->ajaxReturn($data);
	}
	
	public function request_course(){
		$master_course = M("master_course"); // 实例化User对象
		$data = $master_course->where('active=0')->select();
		$this->ajaxReturn($data);
	}
    
    
	
	public function get_config()
	{
	    $M = M();
        $qs = $M->query("
            SELECT pc.grade as grade, c.name as course_name, c.id as course_id,c.desc as description,
                   crw.id as courseware_id, crw.name as courseware_name,crw.url as courseware_url
            FROM anbels_logic_plan_course pc, anbels_master_course c ,anbels_master_courseware crw
            WHERE pc.active = 0 and c.active = 0 and crw.active = 0
                  and pc.course_id = c.id and crw.course_id = c.id
            ORDER BY pc.grade, c.id,crw.id
        ");
        
        $data = array();
        foreach ($qs as $q) {
            if(array_key_exists($q['grade'], $data)){
                if(array_key_exists($q['course_id'], $data[$q['grade']])){
                    array_push($data[$q['grade']][$q['course_id']]['coursewares'] , $q);
                }else{
                    $data[$q['grade']][$q['course_id']] = array('course_name' => $q['course_name'] , 'description' => $q['description'],
                                                                    'coursewares' => array($q)
                                                                    );
                }
            }else{
                $data[$q['grade']] = array($q['course_id'] => array('course_name' => $q['course_name'] , 'description' => $q['description'],
                                                                    'coursewares' => array($q)
                                                                    ));
            }
        }
        
		$this->o('<?xml version="1.0" encoding="utf-8"?>');
		$this->o('<config>');
		$this->o('<version>1.0.0</version>');
		$this->o('<debug>1</debug>');
		$this->o('<urls><loginURL></loginURL></urls>');
		$this->o('<resourcePath value="http://192.168.0.168/Public/swf/"/>');
		$this->o('<courseData>');
        $content = "";
                
        ksort($data);
        foreach ($data as $grade => $course) {
            $this->o('<grade value="'.$grade.'">');
            ksort($course);
            foreach ($course as $course_id => $course_info) {
                $this->o('<course name="'.$course_info['course_name'].'" id="'.$course_id.'" description="'.$course_info['description'].'">');
                foreach ($course_info['coursewares'] as $crw) {
                    $this->o('<courseWare id="'.$crw['courseware_id'].'" name="'.$crw['courseware_name'].'" url="'.$crw['courseware_url'].'" />');
                }
                $this->o('</course>');
            }
            $this->o('</grade>');
        }
		$this->o('</courseData>');
		$this->o('</config>');        
	}


    private function o($str)
    {
        echo htmlentities($str,ENT_COMPAT, 'UTF-8'); 
        echo '<br />';
    }
	
}

	
    