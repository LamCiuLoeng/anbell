<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;


class StatisticController extends BaseController {
    public function index(){
		if(has_all_rules('statistic_admin_view')){
            $this->statistic_list_admin();
            
		}
		
		if(has_all_rules('statistic_teacher_view')){
			// $user_id=session('user_id');
			// $master_school = M("master_school"); // 实例化User对象
			// $school = $master_school->select();
			// $this->assign('school',$school);
			// $this->display('statistic_list_teacher');
			$this->statistic_list_teacher();
		}
	}
    
    public function statistic_list_teacher()
    {
    	$this->classes = M()->query("
				select c.*
				from anbels_logic_class_user cu, anbels_master_class c
				where c.active = 0 and cu.class_id = c.id and cu.role = 'T' and cu.user_id = 
    	".session("user_id")." order by c.grade, c.name");

    	$class_id = I("class_id",null);
    	$course_id = I("course_id",null);
    	if(!$class_id || !$course_id){
    		// $this->error("没有提供班级ID或者课程ID！");
    		$this->display("statistic_list_teacher");
			return;
    	}

    	$this->class_id = $class_id;
    	$this->course_id = $course_id;

		if($course_id){
			$this->courses = $this->get_course_by_class($class_id);
		}else{
			$this->courses = array();
		} 	

		if($class_id && $course_id){
	    	$sql = "
				select t1.* ,t2.score ,t2.times
				from 
				(SELECT u.id as user_id, u.`name` as user_name ,u.system_no as system_no ,c.`name` as class_name
				from anbels_logic_class_user cu , anbels_master_class c ,anbels_auth_user u
				where cu.user_id = u.id and  cu.role = 'S' and cu.class_id = c.id and cu.class_id = ".$class_id." ) t1
				left join 
				(select sl.user_id as user_id, sl.score as score ,sl.times as times
				from anbels_logic_study_log sl 
				where sl.active = 0 and sl.user_type = 'S' and sl.type = 'P' and sl.class_id = ".$class_id." and sl.refer_id = ".$course_id."
				) t2 on  t1.user_id = t2.user_id
	    	";    	
	    	$this->result = M()->query($sql);
		}else{

			
		}
        $this->display("statistic_list_teacher");
    }
    
    public function statistic_list_admin()
    {
        
        $school = $this->get_school();
        $study = $this->get_study_data();
        $percentage = $this->get_school_percentage();
        
        $sl = array();
        foreach ($school as $s) {
            $sl[$s['school_id']] = $s;
        }
        
        foreach ($study as $s) {
            if(array_key_exists($s['school_id'], $sl)){
                $sl[$s['school_id']]['student_involve'] = $s['student_involve'];
                $sl[$s['school_id']]['student_average'] = $s['student_average'];
            }
        }

        foreach ($percentage as $school_id => $school_info) {
            if(array_key_exists($school_id, $sl)){
                if($school_info['total_class'] == 0){
                    $sl[$school_id]['percentage'] = 0;
                }else{
                    $sl[$school_id]['percentage'] =  round($school_info['finish_class'] / intval($school_info['total_class']) * 100);
                }
            }
        }

        
        $this->school_data = $sl;
        $this->display('statistic_list_admin');
    }
    
    
    private function get_school()
    {
        $sql = "
            select s.id as school_id,s.name as school_name ,count(ccu.user_id) as student_total
            from anbels_master_school s 
            left join ( select c.school_id as school_id , cu.user_id as user_id  
                        from anbels_master_class c , anbels_logic_class_user cu 
                        where c.active = 0 and c.id = cu.class_id and cu.role = 'S') ccu
            on s.id = ccu.school_id
            group by s.id,s.name
        ";
        return M()->query($sql);
    }
        
    
    private function get_study_data()
    {
        $sql = "
            select s.id as school_id , count(sl.user_id) as student_involve, 
                   sum(sl.score)/count(sl.user_id) as student_average
            from anbels_master_school s , anbels_logic_plan p ,anbels_logic_study_log sl
            where s.active = 0 and p.active = 0 and sl.active = 0 
                  and s.id = p.school_id and p.id = sl.plan_id and sl.user_type = 'S'
            group by s.id
        ";
        return M()->query($sql);
    }     
    

    private function get_school_percentage(){
        $sql1 = "
            SELECT s.id as school_id,count(c.id) as total_class , 0 as finish_class
                FROM anbels_master_school s, anbels_master_class c
                WHERE s.active = 0 and c.active = 0 and s.id = c.school_id
                GROUP BY s.id
            ";
        $school_basic = M()->query($sql1);
        $sb = array();
        foreach ($school_basic as $s) {
            $sb[$s['school_id']] = array('total_class' => $s['total_class'], 'finish_class' => $s['finish_class'] );
        }


        $sql2 = "
                SELECT s.id as school_id ,pcr.grade as grade ,count(pcr.course_id) as grade_course_no
                FROM anbels_master_school s, anbels_logic_plan p, anbels_logic_plan_course pcr 
                WHERE s.active = 0 and p.active = 0 and pcr.active = 0 and 
                       s.id = p.school_id and p.id = pcr.plan_id
                GROUP BY s.id , pcr.grade 
                ";
        $school_plan = M()->query($sql2);

        $sp = array();
        foreach ($school_plan as $s) {
            if(array_key_exists($s['school_id'], $sp)){
                if(!array_key_exists($s['grade'], $sp[$s['school_id']])){
                    $sp[$s['school_id']][$s['grade']] = $s['grade_course_no'];
                }
            }else{
                $sp[$s['school_id']] = array( $s['grade'] => $s['grade_course_no']);
            }
        }

        $sql3 = "
                SELECT sl.school_id as school_id , sl.grade as grade ,sl.class_id as class_id ,count(sl.refer_id) as course_no
                FROM anbels_logic_study_log sl
                WHERE sl.active = 0 and sl.user_type = 'T'
                GROUP BY sl.school_id , sl.grade , sl.class_id 
        ";

        $clz_info = M()->query($sql3);
        foreach ($clz_info as $ci) {
            if( array_key_exists($ci['school_id'],$sp) && array_key_exists($ci['grade'], $sp[$ci['school_id']]) ){
                if( intval($ci['course_no']) >=  $sp[$ci['school_id']][$ci['grade']]){
                    if(array_key_exists($ci['school_id'], $sb)){
                        $sb[$ci['school_id']]['finish_class'] += 1;
                    }
                }
            }
        }
        return $sb;
    }   


    private function get_course_by_class($class_id){
    	$clz = M("MasterClass")->where(array('active' => 0 ,'id' => intval($class_id)))->find();

    	return M()->query("
			select cr.* 
			from anbels_logic_plan p, anbels_logic_plan_course pc ,anbels_master_course cr 
			where p.active = 0 and cr.active = 0 and pc.active = 0 and 
				  cr.id = pc.course_id and p.id = pc.plan_id and pc.grade = 
    	".$clz['grade']." and p.school_id = ".$clz['school_id']." order by  cr.name ");
    }


    public function ajaxGetCourse(){
    	$class_id = I("class_id",null);
    	$courses = $this->get_course_by_class($class_id);

    	if($courses){
    		$this->ajaxReturn(array('flag' => 0 ,'data' => $courses));
    	}else{
    		$this->ajaxReturn(array('flag' => 1 , 'msg' => '没有该班级的课程！'));
    	}
    }

}