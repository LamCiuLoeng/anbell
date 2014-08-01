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
	
	
    
    public function add()
    {
        $this->locations = gettoplocation();
        $this->display();
    }
    
    public function save_new()
    {
        $m = mydto();
        $m['name'] = I('name',null);
        $m['password'] = I('password',null);
        $m['repassword'] = I('repassword',null);
        $m['gender'] = I('gender',null);
        
        if(!$m['name'] || is_null($m['name'])){
            $this->error("没有填写姓名！");
        }
        
        if(!$m['password'] || is_null($m['password'])){
            $this->error("没有填写密码！");
        }
        
        if($m['password'] != $m['repassword']){
            $this->error("密码与确认密码不一样！");
        }
        
        try{
            $SystemNo = M("SystemNo");
            //$SystemNo->create(array('active'=>0));
            $m['system_no'] =$SystemNo->add();
            $m['password'] = $hashpw = crypt($m['password'],"dingnigefei"); #encrypt the password to save into db
            $m['salt'] = "dingnigefei";  #salt
            $User = M('AuthUser');
            $User->create($m);
            $id = $User->add();
            
            $role = I('role',null);
            $Group = M('AuthGroup');
            $UserGroup = M('AuthUserGroup');
            
            if($role == 'S'){ //student
                $g = $Group->where(array('active' => 0 ,'name' => 'STUDENT'))->find();
                $UserGroup->data(array('user_id' => $id, 'group_id' => $g['id']))->add();
            }elseif($role == 'T'){ //teacher
                $g = $Group->where(array('active' => 0 ,'name' => 'TEACHER'))->find();
                $UserGroup->data(array('user_id' => $id, 'group_id' => $g['id']))->add();
            }
            
            $class_id = I('class_id',null);
            if($class_id){
                $Class = M('MasterClass');
                $ClassUser = M('LogicClassUser');
                $class = $Class->where(array('active' => 0 , 'id' => intval($class_id)))->find();
                $ClassUser->data(array('user_id' => $id,'class_id' => $class['id'],'role' => $role))->add();
            }
            
            $this->success('成功添加账号！','index');
        }catch(Exception $e){
            dump($e);
            $this->error("系统出错，创建不成功！");
        }
        
    }
    
    public function edit()
    {
        echo 'edit';
    }
    
    public function del()
    {
        echo 'del';
    }
    
    public function imp()
    {
        echo 'imp';
    }
    
    public function exp()
    {
        echo 'exp';
    }
}