<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class AccountManagementController extends BaseController {
    public function index(){
		// $search_info=I('post.');
		
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
            $total = $this->get_all_account();
        }elseif(has_all_rules('account_view')){ //teacher
            $this->classes = M()->query("
                    SELECT c.id as id ,c.grade as grade, c.name as name 
                    FROM anbels_logic_class_user cu , anbels_master_class c
                    WHERE c.active = 0 and c.id = cu.class_id and cu.user_id = ".session('user_id')." ;");
            
            $class_id = I("class_id",null);
            $total = $this->get_account($class_id);
            $this->class_id = intval($class_id);
        }else{
            $this->error("没有权限进行该操作！");            
        }
		
        $page = new \Think\Page(count(M()->query($total)), 14);
		$show = $page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
        $sql = $total.' limit '.$page->firstRow.','.$page->listRows;
		
        $users = M()->query($sql);
        $this->auth_user = $users;
		$this->locations = gettoplocation();
        $this->display();
	}
	
	
    private function get_all_account()
    {
		$search_info=I('get.');
		echo($search_info['school_id']);
		if($search_info['school_id']!='') $school_condition="and t.school_id =".$search_info['school_id'];
		if($search_info['location_code_sheng']!='') $sheng_condition="and t.sheng =".$search_info['location_code_sheng'];
		if($search_info['location_code_shi']!='') $shi_condition="and t.shi =".$search_info['location_code_shi'];
		if($search_info['location_code_qu']!='') $qu_condition="and t.qu =".$search_info['location_code_qu'];
        $sql = "
            SELECT 
			t.id, t.system_no, t.user_name, t.gender, 
			t.location_full_name, t.last_login_time, t.role, t.school_name, t.update_by_id,t.school_id, t.qu, t.shi, t.sheng, t.password,
			GROUP_CONCAT(class_name SEPARATOR '<br>') as class_names
			FROM
			(select u.id as id ,u.system_no as system_no, u.name as user_name ,u.gender as gender, u.last_login_time as last_login_time, u.update_by_id as update_by_id,u.password,
			ml.full_name as location_full_name, ll.qu as qu, ll.shi as shi, ll.sheng as sheng, s.name as school_name ,c.name as class_name ,  
			g.display_name as role, s.id as school_id
			from anbels_logic_class_user cu,  anbels_auth_user u, 
			anbels_master_class c,anbels_master_school s,
			anbels_master_location ml, anbels_logic_location ll,
			anbels_auth_group g, anbels_auth_group_access ga
			where cu.user_id = u.id and cu.class_id = c.id and c.school_id = s.id 
			and s.location_id = ml.id and u.active = 0 and ll.qu = ml.`code`
			and ga.uid = u.id and ga.group_id = g.id
			) t
			where 
			(t.system_no like '%".$search_info['keyword']."%' 
			or t.user_name like '%".$search_info['keyword']."%'
			or t.gender like '%".$search_info['keyword']."%' 
			or t.role like '%".$search_info['keyword']."%' 
			or t.location_full_name like '%".$search_info['keyword']."%' 
			or t.school_name like '%".$search_info['keyword']."%'
			or t.class_name like '%".$search_info['keyword']."%')
			".$school_condition."
			".$sheng_condition."
			".$shi_condition."
			".$qu_condition."
			group by 
			t.id
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
            ,g.display_name as role
            from anbels_logic_class_user cu,  anbels_auth_user u , anbels_master_class c ,anbels_master_school s ,anbels_master_location l,
			anbels_auth_group g, anbels_auth_group_access ga
            where u.active = 0 and cu.user_id = u.id and cu.class_id = c.id and c.school_id = s.id and s.location_id = l.id
            and cu.role = 'S' and ga.uid = u.id and ga.group_id = g.id
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
            // $m['salt'] = generatepw(10,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
            //$m['password'] = crypt($m['password'],$m['salt']); #encrypt the password to save into db
            // $m['salt'] = "dingnigefei";  #salt
            $m['password'] = authcode($m['password']);
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
            $school = getUserSchool($user_id);
            $this->school = $school;
            $this->location = M("MasterLocation")->where(array('id' => $school['location_id']))->find();
            $clzs = M()->query("SELECT  c.*
                        FROM anbels_logic_class_user cu,anbels_master_class c
                        WHERE c.id = cu.class_id and cu.user_id = ".$user_id);
            if(in_any_groups("TEACHER",$user_id)){ //is teacher
                if($clzs){
                    $this->user_classes = $clzs;
                }
                $this->school_classes = M("MasterClass")->where(array('active' => 0 ,school_id =>$school['id']))->order("grade,name")->select(); 
                $this->role = 'T';
            }else if(in_any_groups("STUDENT",$user_id)){ //is student
                if($clzs){
                    $this->class = $clzs[0];
                }
                $this->role = 'S';
            }else{
                $this->error("没有该用户类型可以修改！");
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
        $User = M('AuthUser');
        $User->where($map)->setField($m);
        
        
        if(in_any_groups("TEACHER",$map['id'])){ //is teacher
            $clzids = I('class_id',null);
            if($clzids){
                $ClassUser = M('LogicClassUser');
                $ClassUser->where(array('user_id' => $map['id']))->delete();
                foreach ($clzids as $cid) {
                    $ClassUser->create(array('user_id' => $map['id'], 'class_id' => $cid,'role' => 'T'));
                    $ClassUser->add();
                }
            }
        }
        
        $this->success('成功修改账号！',U('AccountManagement/index'));
        
        
        // try{
//            
            // $role = I('role',null);
            // $Group = M('AuthGroup');
            // $UserGroup = M('AuthGroupAccess');
//             
// 
            // if($role == 'S'){ //student
                // $g = $Group->where(array('active' => 0 ,'title' => 'STUDENT'))->find();
                // $UserGroup->where(array('uid' => $id))->setField(array('group_id' => $g['id']));
            // }elseif($role == 'T'){ //teacher
                // $g = $Group->where(array('active' => 0 ,'title' => 'TEACHER'))->find();
                // $UserGroup->where(array('uid' => $id))->setField(array('group_id' => $g['id']));
            // }
//             
            // $class_id = I('class_id',null);
            // if($class_id){
                // $Class = M('MasterClass');
                // $ClassUser = M('LogicClassUser');
                // $class = $Class->where(array('active' => 0 , 'id' => intval($class_id)))->find();
                // $ClassUser->where(array('user_id' => $id))->setField(array('class_id' => $class['id'],'role' => $role));
            // }
//             
            // $this->success('成功修改账号！',U('AccountManagement/index'));
        // }catch(Exception $e){
            // dump($e);
            // $this->error("系统出错，创建不成功！");
        // }
		
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
		// $data['password'] = crypt(I('post.pwc'),"dingnigefei"); #encrypt the password to save into db
		$data['password'] = authcode(I('post.pwc'));
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
        
        $SG = M("AuthGroup");
        $g = $SG->where(array('title' => 'STUDENT'))->find();
        if(!$g || is_null($g)){
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
                // $tmp['salt'] = generatepw(10,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
                $p = generatepw(6,"0123456789");
                // $tmp['password'] = crypt($p,$tmp['salt']);
                $tmp['password'] = authcode($p);
                $M->create($tmp);
                $user_id = $M->add();
                //p($user_id);
				//p($gid);
				//die;
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
	
	public function exp_list()
    {
        if(has_all_rules('account_view_all')){ //admin
            $total = $this->get_all_account();
        }elseif(has_all_rules('account_view')){ //teacher
            $this->classes = M()->query("
                    SELECT c.id as id ,c.grade as grade, c.name as name 
                    FROM anbels_logic_class_user cu , anbels_master_class c
                    WHERE c.active = 0 and c.id = cu.class_id and cu.user_id = ".session('user_id')." ;");
            
            $class_id = I("class_id",null);
            $total = $this->get_account($class_id);
            $this->class_id = intval($class_id);
        }else{
            $this->error("没有权限进行该操作！");            
        }
		
        $page = new \Think\Page(count(M()->query($total)), 24);
		$show = $page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
        $sql = $total.' limit '.$page->firstRow.','.$page->listRows;
		
        $users = M()->query($sql);
        $this->auth_user = $users;
		$this->locations = gettoplocation();
        $this->display();
    }
	
	public function export()
    {
        $total = $this->get_all_account();
		$users = M()->query($total);
		p($users);
    }
    
    
}