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
    
    public function view()
    {
        $id = I('id',null);
        if(!$id || is_null($id)){
            $this->error("没有提供记录的ID！");
        }

        $Q1 = M();   
        $p = $Q1->query("SELECT 
        
                    p.id as id, p.name as name, p.desc as `desc`,l.name as location
                    FROM anbels_master_school s, anbels_logic_plan p,anbels_master_location l
                    WHERE s.id = p.school_id and l.id=s.location_id and p.id = ".$id);
					
		//p($p);
		//die; 
        
        if(!$p || is_null($p)){
            $this->error("该记录不存在！");
        }
        
        if(is_array($p)){
            $p = $p[0];
        }
        
        $Q2 = M();   
        $qs = $Q2->query("SELECT
                       pc.grade as grade, cr.id as cid,cr.name as name
                       FROM anbels_master_course cr, anbels_logic_plan_course pc
                       WHERE cr.active = 0 and pc.active = 0 and pc.course_id = cr.id and 
                       pc.plan_id=".$p['id']." order by grade");
        
		//p($qs);
		//die; 
		
        $result = array();
        foreach ($qs as $q) {
            if(in_array($q['grade'], $result)){
                array_push($result[$q['grade']],$q);
            }else{
                $result[$q['grade']] = array($q);
            }
        }
		 //p($result);
	     //die; 
        
        $this->p = $p;
        $this->pcs = $result;
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
    
	
	public function openflash(){
		$id = I('id',null);
		if(!$id || is_null($id)){
			$this->error("没有提供ID!");
		}
		$this->id = $id;
		$this->display();
	}
    
    public function edit()
    {
        $id = I('id',null);
        if(!$id || is_null($id)){
            $this->error("没有提供ID!");
        }
        
        $p = M('LogicPlan')->where(array('active' => 0 ,'id' => intval($id)))->find();
        if(!$p || is_null($p)){
            $this->error("该记录不存在！");
        }
        $this->p = $p;
        
        $this->courses = M("MasterCourse")->where(array('active' => 0))->order("id")->select();
        
        $qs = M("LogicPlanCourse")->where(array('active' => 0 ,'plan_id' => $p['id']))->order("grade")->select();
        $pc = array();

        foreach ($qs as $q) {
            if(in_array($q['grade'], $pc)){
                $pc[$q['grade']][] = $q;
            }else{
                $pc[$q['grade']] = array($q);
            }
        }
        dump($pc);
        $this->plancourses = $pc;
        $this->display();
    }
    
}