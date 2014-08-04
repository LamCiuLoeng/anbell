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
    
    
}

	
    