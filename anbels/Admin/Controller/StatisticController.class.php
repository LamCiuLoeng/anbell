<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;


class StatisticController extends BaseController {
    public function index(){
		if(has_all_rules('statistic_admin_view')){
			//$user_id=session('user_id');
			$master_school = M("master_school"); // 实例化User对象
			$school = $master_school->select();
			$this->assign('school',$school);
			$this->display('statistic_list_admin');
		}
		
		if(has_all_rules('statistic_teacher_view')){
			$user_id=session('user_id');
			$master_school = M("master_school"); // 实例化User对象
			$school = $master_school->select();
			$this->assign('school',$school);
			$this->display('statistic_list_teacher');
		}
	}

}