<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class PlanController extends BaseController {
    public function index()
    {	
		$Q = M();
        $sql = "SELECT 
                   anbels_master_school.name as school_name,anbels_logic_plan.id as id,
                   anbels_logic_plan.name as name
                   FROM 
                   anbels_master_school ,anbels_logic_plan
                   WHERE
                   anbels_master_school.active = 0 and
                   anbels_logic_plan.active = 0 and
                   anbels_master_school.id = anbels_logic_plan.school_id ";

        if(!has_all_rules("plan_view_all")){
            $user_id = session('user_id');
            $s = M()->query("
                SELECT s.id as school_id
                FROM  anbels_logic_class_user cu ,anbels_master_class c ,anbels_master_school s
                WHERE cu.class_id = c.id and c.school_id = s.id and cu.user_id = ".$user_id." ;");
            if($s && is_array($s)){
                $sql .= " and anbels_logic_plan.school_id = ".$s[0]['school_id']." ";
            }else{
                $sql .= " and anbels_logic_plan.school_id = 0 "; #don't show any school plan
            }
        }

		$plan = $Q->query($sql);
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
        
                    p.id as id, p.name as name, p.desc as `desc`,l.full_name as location
                    FROM anbels_master_school s, anbels_logic_plan p,anbels_master_location l
                    WHERE s.id = p.school_id and l.id=s.location_id and p.id = ".$id);
        
        if(!$p || is_null($p)){
            $this->error("该记录不存在！");
        }
        
        if(is_array($p)){
            $p = $p[0];
        }
		//get the user's class
		$clzs = M()->query("SELECT c.*
		            FROM anbels_logic_class_user cu,anbels_master_class c
		            WHERE cu.class_id = c.id and cu.user_id = ".session('user_id'));
		if(!$clzs || is_null($clzs)){
		    $this->error("该用户没有相应的班级！");
		}
        
        $user_clzs = array();
        foreach ($clzs as $c) {
            if(array_key_exists($c['grade'], $user_clzs)){
                $user_clzs[$c['grade']][] = $c;
            }else{
                $user_clzs[$c['grade']] = array($c);
            }
        }
        $this->user_clzs = $user_clzs;
        
        $Q2 = M();   
        $qs = $Q2->query("SELECT
                       pc.grade as grade, cr.id as cid,cr.name as course_name,crw.name as courseware_name
                       FROM 
                            anbels_master_course cr, 
                            anbels_logic_plan_course pc ,
                            anbels_master_courseware crw
                       WHERE 
                             cr.active = 0 and pc.active = 0 
                             and pc.course_id = cr.id and crw.course_id = cr.id
                             and pc.plan_id=".$p['id']." order by grade"); 
        $result = array();
        foreach ($qs as $q) {
            if(!array_key_exists($q['grade'], $user_clzs)){ continue; } //filter out the course don't in the user's class
            if(array_key_exists($q['grade'], $result)){
                if(array_key_exists($q['cid'], $result[$q['grade']])){
                    //array_push($result[$q['grade']][$q['cid']]['courseware'],$q);
                    $result[$q['grade']][$q['cid']]['courseware'][] = $q;
                }else{
                    $result[$q['grade']][$q['cid']] = array(
                                                            'name' => $q['course_name'],
                                                            'courseware' => array($q),
                                                            );
                }
            }else{
                $result[$q['grade']] = array( $q['cid'] => array(
                                                                'name' => $q['course_name'],
                                                                'courseware' => array($q),
                                                                ));
            }
        }
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

        $cid = I('cid',null);
        if(!$cid || is_null($cid)){
            $this->error("没有提供班级的ID!");   
        }
        $school = getUserSchool(session('user_id'));
        $plan = M("LogicPlan")->where(array('active' => 0 ,'school_id' => $school['id']))->find();
        $clz = M('MasterClass')->where(array('id' => $cid))->find();

        $this->school_id = $school['id'];
        $this->plan_id = $plan['id'];
        $this->class_id = $clz['id'];
        $this->grade = $clz['grade'];
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
            if(  array_key_exists($q['grade'], $pc) ){
                $pc[$q['grade']][] = $q;
            }else{
                $pc[$q['grade']] = array($q);
            }
        }
        $this->plancourses = $pc;
        $this->grades = array_keys($pc);
        $this->display();
    }
    
    public function save_edit()
    {
        $id = I('id',null);
        if(!$id || is_null($id)){
            $this->error("没有提供ID！");
        }
        
        $M = M("LogicPlan");
        $p = $M->where(array('active' => 0 ,'id' => intval($id)))->find();
        if(!$p || is_null($p)){
            $this->error("该记录不存在！");
        }
        
        $dto = mydto_edit();
        $dto['name'] = I('name',null);
        $dto['desc'] = I('desc',null);
        $M->where(array('id' => $p['id']))->save($dto);
        
        //update the existing related course
        $PC = M("LogicPlanCourse");
        $cids = M()->query("select id from anbels_logic_plan_course where active = 0 and plan_id = ".$p['id']);
        foreach ($cids as $cid) {
            $del = I("delcourse_".$cid['id'],null);
            echo $cid['id'];
            if($del && $del == 'YES'){ //delete the record
                $tmp = mydto();
                $tmp['active'] = 1;
                $result = $PC->where(array('id' => $cid['id']))->save($tmp);
            }else{  //update the record
                $oldcid = I("oldcourse_".$cid['id'],null);
                if($oldcid){
                    $tmp = mydto();
                    $tmp['course_id'] = $oldcid;
                    $PC->where(array('id' => $cid['id']))->save($tmp);
                }
            }
        }
        
        //add the new 
        for($i=1;$i<10;$i++){
            $news  = I("course_".$i,null);
            if($news && is_array($news)){
                foreach ($news as $new) {
                    if($new){
                        $tmp = mydto();
                        $tmp['plan_id'] = intval($p['id']);
                        $tmp['grade'] = $i;
                        $tmp['course_id'] = $new;
                        $PC->create($tmp);
                        $PC->add();
                    }   
                }
            }
        }
        
        $this->success('成功修改教学计划！',U('Plan/index'),10);
    }

	public function del()
	{
		$id = I('id',null);
		if(!$id || is_null($id)){
			$this->error("没有提供ID！");
		}
				
		$M = M("LogicPlan");
		$p = $M->where(array('active' => 0 , 'id' => intval($id)))->find();
		if(!$p || is_null($p)){
			$this->error("该记录不存在！");
		}
		
		$tmp = mydto();
		unset($tmp['create_time']);
		unset($tmp['create_by_id']);
		$tmp['active'] = 1;
		$M->where(array('id' => $p['id']))->save($tmp);
		$this->success('成功删除教学计划！',U('Plan/index'));
	}
}