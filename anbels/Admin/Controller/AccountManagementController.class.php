<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class AccountmanagementController extends BaseController {
    public function index(){	
		$User = M('AuthUser');
        $users = $User
        ->join('left join anbels_logic_class_user  on  anbels_auth_user.id = anbels_logic_class_user.user_id')
        ->join('left join anbels_master_class on anbels_logic_class_user.class_id = anbels_master_class.id')
        ->join('left join anbels_master_school on anbels_master_school.id = anbels_master_class.school_id')
        ->field('anbels_auth_user.id,anbels_auth_user.system_no,anbels_auth_user.name as user_name,
                 anbels_auth_user.gender,anbels_auth_user.last_login_time,
                 anbels_master_school.name as school_name,anbels_master_class.name as class_name')
        //->order('anbels_auth_user.system_no desc')
        ->select();

        $this->auth_user = $users;
        $this->display();
	}
	
	
}