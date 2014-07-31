<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class PlanController extends BaseController {
    public function index()
    {
        $Plan = M("LogicPlan");
        $plan = $Plan->join("anbels_master_school on anbels_master_school.id = anbels_logic_plan.school_id")
        ->where(array('anbels_logic_plan.active' => 0))
        ->order("anbels_master_school.name,anbels_logic_plan.grade")
        ->field("anbels_master_school.name as school_name,anbels_logic_plan.id as id,anbels_logic_plan.name as name,anbels_logic_plan.grade as grade")
        ->select();
        $this->plans = $plan;
        $this->display();
    } 
    
    public function add()
    {
        $Course = M("MasterCourse");
        $this->courses = $Course->where(array('active' => 0 ))->order('name')->select();
        $this->display();
    }
    
    public function save_new()
    {
        try{
            $m = mydto();
            $m['class_id'] = I('class_id',null);
            $m['name'] = I('name',null);
            $m['grade'] = I('grade',null);
            $m['desc'] = I('desc',null);
            
            $LogicPlan = M("LogicPlan");
            $LogicPlan->create($m);
            $pid = $LogicPlan->add();
            
            $courses = I('course',null);
            if($courses && is_array($courses)){
                $LogicPlanCourse = M('LogicPlanCourse');
                foreach ($courses as $course) {
                    if($course && $course != ''){
                        $LogicPlanCourse->data(array('course_id' => intval($course), 'plan_id' => $pid ))->add();
                    }            
                }            
            }
            $this->success('成功添加教学计划！',U('Plan/index'));
        }catch(Exception $e){
            dump($e);
            $this->error("系统出错，创建不成功！");
        }
            
                

    }
    
}