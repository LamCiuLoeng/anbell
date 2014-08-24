<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class AccountManagementController extends BaseController {
    public function index(){
		// $search_info=I('post.');
		// $this->locations = gettoplocation();
		// if($search_info['location_code'][0]!='') $map['anbels_logic_location.sheng']  = array('eq',$search_info['location_code'][0]);
		// if($search_info['location_code'][1]!='') $map['anbels_logic_location.shi']  = array('eq',$search_info['location_code'][1]);
		// if($search_info['location_code'][2]!='') $map['anbels_master_location.code']  = array('eq',$search_info['location_code'][2]);
		// if($search_info['school_id']!='') $map['anbels_master_school.id']  = array('eq',$search_info['school_id']);
		// if($search_info['class_id']!='') $map['anbels_master_class.id']  = array('eq',$search_info['class_id']);
// 		
// 		
		// $map['anbels_auth_user.name|anbels_auth_user.system_no|anbels_master_location.full_name|anbels_master_school.name|anbels_auth_group.display_name']  = array('like','%'.$search_info['keyword'].'%');
		// $map['anbels_auth_user.active']  = array('eq','0');
		// $map['_logic'] = 'and';
		// $User = M('AuthUser');
        // $users = $User
        // ->join('left join anbels_logic_class_user  on  anbels_logic_class_user.user_id = anbels_auth_user.id')
        // ->join('left join anbels_master_class on anbels_master_class.id = anbels_logic_class_user.class_id')
        // ->join('left join anbels_master_school on anbels_master_school.id = anbels_master_class.school_id')
		// ->join('left join anbels_master_location on anbels_master_location.id = anbels_master_school.location_id')
		// ->join('left join anbels_logic_location on anbels_logic_location.qu = anbels_master_location.code')
// 		
		// ->join('left join anbels_auth_group_access on anbels_auth_group_access.uid = anbels_auth_user.id')
		// ->join('left join anbels_auth_group on anbels_auth_group.id = anbels_auth_group_access.group_id')
// 		
        // ->field('anbels_auth_user.id,anbels_auth_user.system_no,anbels_auth_user.name as user_name,anbels_auth_user.gender,anbels_auth_user.last_login_time,
                 // anbels_master_school.name as school_name,anbels_master_class.name as class_name,
				 // anbels_master_location.full_name,
				 // anbels_auth_group.display_name')
        // ->order('anbels_auth_user.id desc')
		// ->where($map)
        // ->select();
       
        if(has_all_rules('account_view_all')){ //admin
            $sql = $this->get_all_account();
        }elseif(has_all_rules('account_view')){ //teacher
            $this->classes = M()->query("
                    SELECT c.id as id ,c.grade as grade, c.name as name 
                    FROM anbels_logic_class_user cu , anbels_master_class c
                    WHERE c.active = 0 and c.id = cu.class_id and cu.user_id = ".session('user_id')." ;");
            
            $class_id = I("class_id",null);
            $sql = $this->get_account($class_id);
            $this->class_id = intval($class_id);
        }else{
            $this->error("没有权限进行该操作！");            
        }
        
        $users = M()->query($sql);
        $this->auth_user = $users;
        $this->display();
	}
	
	
    private function get_all_account()
    {
        $sql = "
            SELECT 
            t.id,t.system_no, t.user_name ,t.gender, 
            t.location_full_name ,t.school_name , t.last_login_time ,t.role
            ,GROUP_CONCAT(class_name SEPARATOR ',') as class_names
            FROM
            (select u.id as id ,u.system_no as system_no, u.name as user_name ,u.gender as gender, 
            l.full_name as location_full_name ,s.name as school_name ,c.name as class_name , u.last_login_time as last_login_time
            , cu.role as role
            from anbels_logic_class_user cu,  anbels_auth_user u , anbels_master_class c ,anbels_master_school s ,anbels_master_location l
            where cu.user_id = u.id and cu.class_id = c.id and c.school_id = s.id and s.location_id = l.id and u.active = 0
            ) t
            group by 
            t.id,t.system_no, t.user_name ,t.gender, 
            t.location_full_name ,t.school_name , t.last_login_time, t.role
            order by t.system_no
        ";
        return $sql;
    }
    
    private function get_account($class_id)
    {
        $user_id = session('user_id');
        $andsql = "";
        if(!$class_id){
            $class = M("LogicClassUser")->where(array('user_id'=>$user_id))->select();
            $cids = array();
            foreach ($class as $c) {
                $cids[] = $c['class_id'];
            }   
            $andsql = "and cu.class_id in (".join(",",$cids).")";
        }else{
            $andsql = "and cu.class_id = ".$class_id;
        }
        
        
        $sql = "
            SELECT 
            t.id,t.system_no, t.user_name ,t.gender, 
            t.location_full_name ,t.school_name , t.last_login_time ,t.role
            ,GROUP_CONCAT(class_name SEPARATOR ',') as class_names
            FROM
            (select u.id as id ,u.system_no as system_no, u.name as user_name ,u.gender as gender, 
            l.full_name as location_full_name ,s.name as school_name ,c.name as class_name , u.last_login_time as last_login_time
            ,cu.role as role
            from anbels_logic_class_user cu,  anbels_auth_user u , anbels_master_class c ,anbels_master_school s ,anbels_master_location l
            where u.active = 0 and cu.user_id = u.id and cu.class_id = c.id and c.school_id = s.id and s.location_id = l.id
            and cu.role = 'S' 
        ";       
        $sql .= $andsql;
        $sql .= "
            ) t
            group by 
            t.id,t.system_no, t.user_name ,t.gender, 
            t.location_full_name ,t.school_name , t.last_login_time , t.role
            order by t.system_no";
        return $sql;           

    }
    
    
    
    
    public function add()
    {
        if(has_all_rules("account_add")){
            if(has_all_rules("account_view_all")){  //is admin
                $this->locations = gettoplocation();
            }else if(has_all_rules("account_view")){  //is teacher
                $school = getUserSchool(session('user_id'));
                if($school){
                    $this->school = $school;
                    $this->location = M("MasterLocation")->where(array('id' => $school['location_id']))->find();
                    
                    $this->class = M()->query("
                        SELECT c.id,c.name,c.grade
                        FROM anbels_master_class c, anbels_logic_class_user cu
                        WHERE c.id = cu.class_id and cu.user_id = ".session('user_id')." order by c.grade,c.name");
                }            
            }
        }else{
            $this->error("没该操作的权限！");
        }
        $this->display();
    }
    
    public function save_new()
    {
		$class_id = I('class_id',null);
        $m = mydto();
        $m['name'] = I('name',null);
        $m['password'] = I('password',null);
        $m['repassword'] = I('repassword',null);
        $m['gender'] = I('gender',null);
		
		if(!$class_id || is_null($class_id)){
            $this->error("请选择班级！");
        }
        
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

            
            $Class = M('MasterClass');
            $ClassUser = M('LogicClassUser');
            if(has_all_rules('account_view_all')){ //admin
                if(is_array($class_id)){
                    foreach ($class_id as $cid) {
                        if($cid){
                            $ClassUser->data(array('user_id' => $id,'class_id' => $cid,'role' => $role))->add();
                        }
                    }
                }
                
            }elseif(has_all_rules('account_view')){ //teacher
                //teacher only can add student account            
                $ClassUser->data(array('user_id' => $id,'class_id' => $class_id,'role' => $role))->add();
            }

            $this->success('成功添加账号！',U('AccountManagement/index'));
        }catch(Exception $e){
            dump($e);
            $this->error("系统出错，创建不成功！");
        }
        
    }
    
    public function edit()
    {
        $user_id = I('id',null);
		if($user_id)
		{
		    $U = M("AuthUser");
            $user = $U->where(array('active' => 0 , 'id' =>$user_id))->find();
            if(!$user || is_null($user)){
                $this->error("记录不存在！");
            }
            $this->user = $user;
            
		    if(has_all_rules('account_view_all')){ //admin
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
		    
            }elseif(has_all_rules('account_view')){ //teacher
                $school = getUserSchool($user_id);
                $this->school = $school;
                $this->location = M("MasterLocation")->where(array('id' => $school['location_id']))->find();
                
            }
            
            
            
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
	
	public function pw_edit()
    {
		$id = I('id',null);
        if(!$id || is_null($id)){
            $this->error("没有提供ID！");
        }
        
        $auth_user=M('auth_user')->where(array('active' => 0 ,'id' => intval($id)))->find();
        if(!$auth_user || is_null($auth_user)){
            $this->error("该记录不存在！");
        }
		$this->assign('auth_user',$auth_user);
		$this->display();
    }
	
	public function pw_edit_handle()
    {
		$map['id'] = I('post.id');
		$data['password'] = crypt(I('post.pwc'),"dingnigefei"); #encrypt the password to save into db
		$auth_user=M('auth_user');
		if($auth_user->where($map)->setField($data))
		{
			$this->success('密码重置成功！',U('index'));
		} 
		else 
		{
			$this->error('密码重置改失败，请重试...');	
		}
    }
	
	
    
    public function del()
    {
		$ids = I("id",null);
		$checkbox_array=$ids;
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
        $location_code = I("location_code",null);
        if(!$location_code || is_null($location_code)){ $this->error("请选择需要导入账号的地区！"); }
        
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
                    'saveName'   =>    array('date','YmdHis'),    
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
            $path =  './Public/'.$info['savepath'].$info['savename'];
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
                $school_id = $this->get_or_create_school($school_name,$location_code);
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


    private function get_or_create_school($name,$locaton_code)
    {
        $M = M("MasterSchool");
        $l = M("MasterLocation")->where(array('code' => $locaton_code))->find();
        $s = $M->where(array('active' => 0 , 'location_id' => $l['id'] , 'name' => $name))->find();
        if(!$s || is_null($s)){
            $t = mydto();
            $t['name'] = $name;
            $t['location_id'] = $l['id'];
            $M->create($t);
            return $M->add();
        }else{
            return $s['id'];
        }
    }


    private function get_or_create_class($class_name,$grade,$school_id)
    {
        $M = M();
        $sql = "select c.id as id
                   FROM anbels_master_class c, anbels_master_school s
                   WHERE s.id = c.school_id and s.id = ".$school_id." and c.grade= ".$grade." and c.name= '".$class_name."' ;";
        $result = $M->query($sql);
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
        $m['role'] = 'S';
        $M->create($m);
        return $M->add();
    }
    
    
}