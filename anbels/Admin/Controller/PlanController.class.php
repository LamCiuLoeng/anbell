<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class PlanController extends BaseController {
    public function index()
    {	
		$Q = M();
		$plan = $Q->query("SELECT 
				   anbels_master_school.name as school_name,anbels_logic_plan.id as id,
				   anbels_logic_plan.name as name
				   FROM 
				   anbels_master_school ,anbels_logic_plan
				   WHERE
				   anbels_master_school.active = 0 and
				   anbels_logic_plan.active = 0 and
				   anbels_master_school.id = anbels_logic_plan.school_id
		");
        $this->plans = $plan;
        $this->display();
    } 
    
    public function add()
    {
        $Course = M("MasterCourse");
        $this->courses = $Course->where(array('active' => 0 ))->order('name')->select();
		$this->locations = gettoplocation();		
        $this->display();
    }
		
    
    public function save_new()
    {
        $m = mydto();
        $m['school_id'] = I('school_id',null);
        $m['name'] = I('name',null);
        $m['desc'] = I('desc',null);
        
        $LogicPlan = M("LogicPlan");
        $LogicPlan->create($m);
        $pid = $LogicPlan->add();
        
        $LogicPlanCourse = M('LogicPlanCourse');
        for($i=1;$i<10;$i++){
        	$cs = I('course_'.$i,null);
			if(is_array($cs)){
				for ($j=0; $j < count($cs); $j++) {
					$c = $cs[$j];
					if(!$c){ continue; }
					$tmp = mydto();
					$tmp['active'] = 0;
					$tmp['plan_id'] = $pid;
					$tmp['grade'] = $i;		
					$tmp['course_id'] = intval($c);	
					$LogicPlanCourse->data($tmp)->add();
				}
			}
        }
        $this->success('成功添加教学计划！',U('Plan/index'));
    }
    
}