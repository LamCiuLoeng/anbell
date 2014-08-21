<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class AccountManagementController extends BaseController {
    public function index(){
		$search_info=I('post.');
		$this->locations = gettoplocation();
		if($search_info['location_code'][0]!='') $map['anbels_logic_location.sheng']  = array('eq',$search_info['location_code'][0]);
		if($search_info['location_code'][1]!='') $map['anbels_logic_location.shi']  = array('eq',$search_info['location_code'][1]);
		if($search_info['location_code'][2]!='') $map['anbels_master_location.code']  = array('eq',$search_info['location_code'][2]);
		if($search_info['school_id']!='') $map['anbels_master_school.id']  = array('eq',$search_info['school_id']);
		if($search_info['class_id']!='') $map['anbels_master_class.id']  = array('eq',$search_info['class_id']);
		
		
		$map['anbels_auth_user.name|anbels_auth_user.system_no|anbels_master_location.full_name|anbels_master_school.name|anbels_auth_group.display_name']  = array('like','%'.$search_info['keyword'].'%');
		$map['anbels_auth_user.active']  = array('eq','0');
		$map['_logic'] = 'and';
		$User = M('AuthUser');
        $users = $User
        ->join('left join anbels_logic_class_user  on  anbels_logic_class_user.user_id = anbels_auth_user.id')
        ->join('left join anbels_master_class on anbels_master_class.id = anbels_logic_class_user.class_id')
        ->join('left join anbels_master_school on anbels_master_school.id = anbels_master_class.school_id')
		->join('left join anbels_master_location on anbels_master_location.id = anbels_master_school.location_id')
		->join('left join anbels_logic_location on anbels_logic_location.qu = anbels_master_location.code')
		
		->join('left join anbels_auth_group_access on anbels_auth_group_access.uid = anbels_auth_user.id')
		->join('left join anbels_auth_group on anbels_auth_group.id = anbels_auth_group_access.group_id')
		
        ->field('anbels_auth_user.id,anbels_auth_user.system_no,anbels_auth_user.name as user_name,anbels_auth_user.gender,anbels_auth_user.last_login_time,
                 anbels_master_school.name as school_name,anbels_master_class.name as class_name,
				 anbels_master_location.full_name,
				 anbels_auth_group.display_name')
        ->order('anbels_auth_user.id desc')
		->where($map)
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
           // $SystemNo->create(array('active'=>0));
            $m['system_no'] =$SystemNo->add(array('active'=>0));
            $m['password'] = crypt($m['password'],"dingnigefei"); #encrypt the password to save into db
            $m['salt'] = "dingnigefei";  #salt
            $User = M('AuthUser');
            $User->create($m);
            $id = $User->add();
			//p($id);
			//die;
            
            $role = I('role',null);
            $Group = M('AuthGroup');
            $UserGroup = M('AuthGroupAccess');
            

            if($role == 'S'){ //student
                $g = $Group->where(array('active' => 0 ,'title' => 'STUDENT'))->find();
                $UserGroup->data(array('uid' => $id, 'group_id' => $g['id']))->add();
            }elseif($role == 'T'){ //teacher
                $g = $Group->where(array('active' => 0 ,'title' => 'TEACHER'))->find();
                $UserGroup->data(array('uid' => $id, 'group_id' => $g['id']))->add();
            }
            
            $class_id = I('class_id',null);
            if($class_id){
                $Class = M('MasterClass');
                $ClassUser = M('LogicClassUser');
                $class = $Class->where(array('active' => 0 , 'id' => intval($class_id)))->find();
                $ClassUser->data(array('user_id' => $id,'class_id' => $class['id'],'role' => $role))->add();
            }
            
            $this->success('成功添加账号！',U('AccountManagement/index'));
        }catch(Exception $e){
            dump($e);
            $this->error("系统出错，创建不成功！");
        }
        
    }
    
    public function edit()
    {
       
		if(I('post.checkbox'))
		{
			$this->locations = gettoplocation();
			$checkbox_array=I('post.checkbox');
			$map['anbels_auth_user.id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$auth_user=M('auth_user')
			->join('left join anbels_logic_class_user  on  anbels_logic_class_user.user_id = anbels_auth_user.id')
			->join('left join anbels_master_class on anbels_master_class.id = anbels_logic_class_user.class_id')
			->join('left join anbels_master_school on anbels_master_school.id = anbels_master_class.school_id')
			
			->join('left join anbels_auth_group_access on anbels_auth_group_access.uid = anbels_auth_user.id')
			->join('left join anbels_auth_group on anbels_auth_group.id = anbels_auth_group_access.group_id')
			
			->field('anbels_auth_user.id,anbels_auth_user.gender,anbels_auth_user.name as user_name,
					anbels_master_school.name as school_name,
					anbels_master_class.id as class_id,anbels_master_class.name as class_name,
					anbels_auth_group.id as group_id,anbels_auth_group.display_name as group_name,
					anbels_logic_class_user.role as role')
			->where($map)
			->select();
		
			$this->assign('auth_user',$auth_user);
			$this->display();
		} 
		else 
		{
			$this->error('请选择所要修改的账号，重试...','',2);	
		}

    }
	
	public function edit_handle()
    {
		$map['id'] = I('post.id');
		
		$m['name'] = I('name',null);
        $m['update_by_id'] = session('user_id');
		$m['update_time'] = mynow();
        $m['gender'] = I('gender',null);
        
        if(!$m['name'] || is_null($m['name'])){
            $this->error("没有填写姓名！");
        }
        
        try{
            $m['salt'] = "dingnigefei";  #salt
			$id = I('post.id');
            $User = M('AuthUser');
            $User->where($map)->setField($m);
			//p($m);
			//die;
            
            $role = I('role',null);
            $Group = M('AuthGroup');
            $UserGroup = M('AuthGroupAccess');
            

            if($role == 'S'){ //student
                $g = $Group->where(array('active' => 0 ,'title' => 'STUDENT'))->find();
                $UserGroup->where(array('uid' => $id))->setField(array('group_id' => $g['id']));
            }elseif($role == 'T'){ //teacher
                $g = $Group->where(array('active' => 0 ,'title' => 'TEACHER'))->find();
                $UserGroup->where(array('uid' => $id))->setField(array('group_id' => $g['id']));
            }
            
            $class_id = I('class_id',null);
            if($class_id){
                $Class = M('MasterClass');
                $ClassUser = M('LogicClassUser');
                $class = $Class->where(array('active' => 0 , 'id' => intval($class_id)))->find();
                $ClassUser->where(array('user_id' => $id))->setField(array('class_id' => $class['id'],'role' => $role));
            }
            
            $this->success('成功修改账号！',U('AccountManagement/index'));
        }catch(Exception $e){
            dump($e);
            $this->error("系统出错，创建不成功！");
        }
		
	}
    
    public function del()
    {
		$checkbox_array=I('post.checkbox');

		$auth_user = M("auth_user"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		//p($map);
		//die;
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($auth_user->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('AccountManagement/index'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的账号...');	
		}
    }
    
    public function imp()
    {
        $this->locations = gettoplocation();
        $this->display();
    }
    
    public function imp_handle()
    {
        $location_id = I("location_id",null);
        if(!$location_id || is_null($location_id)){ $this->error("请选择需要导入账号的地区！"); }
        
        $SG = M("MuthGroup");
        $g = $SG->where(array('title' => 'STUDENT'))->find();
        if(!g || is_null($g)){
            $this->error("没有学生的角色，请先创建学生的角色！");
        }else{
            $gid = $g['id'];
        }
        
        
        $config = array(    
                    'rootPath'   =>    './Public/',
                    'savePath'   =>    'Upload/',    
                    'saveName'   =>    array('uniqid',''),    
                    //'exts'       =>    array('csv', 'xls', 'xlsx'),    
                    'autoSub'    =>    true,    
                    'subName'    =>    array('date','Ymd','time'),
                 );
        $upload = new \Think\Upload($config);// 实例化上传类
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }    
        $info   =   $upload->uploadOne($_FILES["xls"]);  
        if($info) {
        
            // 上传成功      
            $M = M("AuthUser");         
            $path = './Public/'.$info['savepath'].$info['savename'];
            $data = readXLS($path);
            // dump($data);
            $index = 0;
            $succ = 0;

            foreach ($data as $row) {
                $index += 1;
                if($index == 1){ continue ;} //excel header
                
                $school_name = $row["A"];
                $grade = $row["B"];
                $class_name = $row["C"];
                $school_id = $this->get_or_create_school($school_name,$location_id);
                $class_id = $this->get_or_create_class($class_name, $grade, $school_id);

                $tmp = mydto();
                $tmp['system_no'] = M("SystemNo")->add(array('active'=>0));
                $tmp['name'] = $row["D"];
                $tmp['gender'] = $row["E"];                
                $tmp['salt'] = generatepw(10,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
                $p = generatepw(6,"0123456789");
                $tmp['password'] = crypt($p,$tmp['salt']);
                $M->create($tmp);
                $user_id = $M->add();
                
                $GU = M("AuthGroupAccess");
                $GU->create(array("uid" => $user_id, "group_id" => $gid));
                $GU->add();
                
                $result = $this->add_user_to_class($user_id, $class_id);
                if($result){
                    $succ ++;
                }
                
                
                
            }                               
        }else{
            $this->error($upload->getError()); 
        }
        
        $this->success('批量导入成功,共创建了'.$succ.'个账号！',U('AccountManagement/index'));
    }


    private function get_or_create_school($name,$locaton_id)
    {
        $M = M("MasterSchool");
        $s = $M->where(array('active' => 0 , 'location_id' => $locaton_id , 'name' => $name))->find();
        if(!$s || is_null($s)){
            $t = mydto();
            $t['name'] = $name;
            $t['location_id'] = $locaton_id;
            $M->create($t);
            return $M->add();
        }else{
            return $s['id'];
        }
    }


    private function get_or_create_class($class_name,$grade,$school_id)
    {
        $M = M();
        $result = $M->query("select c.id as id
                   FROM anbels_master_class c, anbels_master_school s
                   WHERE s.id = c.school_id and s.id = ".$school_id." and c.grade= ".$grade." and c.name= ".$class_name." ;"
        );
        if(!$result || is_null($result)){
            $C = M("MasterClass");
            $t=mydto();
            $t['school_id']= $school_id;
            $t['grade']= $grade;
            $t['name']=$class_name;
            $C->create($t);
            return $C->add();
        }else{
             return $result[0]['id'];  
        }
    }
    
    private function add_user_to_class($user_id,$class_id)
    {
        $M = M("LogicClassUser");
        $m['user_id'] = $user_id;
        $m['class_id'] = $class_id;
        $M->create($m);
        return $M->add();
    }
    
    
}