<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class MasterController extends BaseController {
    public function index(){
		//echo 'hello';
		//p($_GET);die;
		//echo U('index/master@blog.thinkphp.cn',array('uid'=>1));die;
        $this->display();
	}
	
	public function school_list(){
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
			
		$master_school = M('master_school')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$this->assign('master_school',$master_school);// 赋值数据集
		$count = M('master_school')->where('active=0')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		die();
		$master_school=M('master_school')->where('active=0')->select();
		$this->assign('master_school',$master_school);
		$this->display();
	}
	
	public function school_add(){
        $this->display();
	}
	public function school_add_handle(){
		$master_school = M("master_school"); // 实例化User对象
		$location_id=M('master_location')->where('code='.I('post.location'))->find();
		$data['location_id'] = $location_id['id'];
		$data['name'] = I('post.school_name');
		$data['desc'] = I('post.description');
		$data['active'] = 0;
		$data['create_by_id'] = session('user_id');
		$data['create_time'] = mynow();
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->add($data));die;
		if($master_school->add($data))
		{
			$this->success('学校添加成功！',U('master/school_list'));
		} 
		else 
		{
			$this->error('学校添加失败，请重试...');	
		}
	}
	
	public function school_edit(){
		if(I('post.checkbox'))
		{
			$checkbox_array=I('post.checkbox');
			$map['id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$master_school=M('master_school')->where($map)->select();
			$this->assign('master_school',$master_school);
			$this->display();
		} 
		else 
		{
			$this->error('请选择所要修改的学校，重试...','',2);	
		}
	}
	
	public function school_edit_handle(){
		$master_school = M("master_school"); // 实例化User对象
		$map['id'] = I('post.id');
		$location_id=M('master_location')->where('code='.I('post.location'))->find();
		$data['location_id'] = $location_id['id'];
		//$data['location_id'] = I('post.location');
		$data['name'] = I('post.school_name');
		$data['desc'] = I('post.description');
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		if($master_school->where($map)->setField($data))
		{
			$this->success('学校修改成功！',U('master/school_list'));
		} 
		else 
		{
			$this->error('学校修改失败，请重试...');	
		}
	}
	
	public function school_list_delete_handle(){
		$checkbox_array=I('post.checkbox');
		//$aaaa=$_POST['checkbox'];
		//p($dddd);
		//die;
		$master_school = M("master_school"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($master_school->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('master/school_list'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的学校...');	
		}
	}
	
	public function class_list(){
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
			
		$master_class = M('master_class')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$this->assign('master_class',$master_class);// 赋值数据集
		$count = M('master_class')->where('active=0')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		die();
		$master_class=M('master_class')->where('active=0')->select();
		$this->assign('master_class',$master_class);
		$this->display();
	}
    
	public function class_add(){
        $this->display();
	}
	public function class_add_handle(){
		$master_class = M("master_class"); // 实例化User对象
		$data['school_id'] = I('post.school_name');
		$data['name'] = I('post.class_name');
		$data['grade'] = I('post.grade');
		$data['desc'] = I('post.description');
		$data['active'] = 0;
		$data['create_by_id'] = session('user_id');
		$data['create_time'] = mynow();
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->add($data));die;
		if($master_class->add($data))
		{
			$this->success('班级添加成功！',U('master/class_list'));
		} 
		else 
		{
			$this->error('班级添加失败，请重试...');	
		}
	}
	
	public function class_edit(){
		if(I('post.checkbox'))
		{
			$checkbox_array=I('post.checkbox');
			$map['id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$master_class=M('master_class')->where($map)->select();
			$this->assign('master_class',$master_class);
			$this->display();
		} 
		else 
		{
			$this->error('请选择所要修改的班级，重试...','',2);	
		}
	}
	
	public function class_edit_handle(){
		$master_class = M("master_class"); // 实例化User对象
		$map['id'] = I('post.id');
		$data['school_id'] = I('post.school_name');
		$data['name'] = I('post.class_name');
		$data['grade'] = I('post.grade');
		$data['desc'] = I('post.description');
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		if($master_school->where($map)->setField($data))
		{
			$this->success('班级修改成功！',U('master/school_list'));
		} 
		else 
		{
			$this->error('班级修改失败，请重试...');	
		}
	}
	
	public function class_list_delete_handle(){
		$checkbox_array=I('post.checkbox');
		//$aaaa=$_POST['checkbox'];
		//p($dddd);
		//die;
		$master_class = M("master_class"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($master_class->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('master/class_list'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的班级...');	
		}
	}
	
	
	public function course_list(){
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
			
		$master_course = M('master_course')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$this->assign('master_course',$master_course);// 赋值数据集
		$count = M('master_course')->where('active=0')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		die();
		$master_course=M('master_course')->where('active=0')->select();
		$this->assign('master_course',$master_course);
		$this->display();
	}
	
	public function course_add(){
        $this->display();
	}
	
	public function course_add_handle(){
		$m = mydto();
		$m['name'] = I('name',null);
		if(!$m['name'] || is_null($m['name'])){
			$this->error("课程名称不能为空！");
		}
		
		
		$m['desc'] = I('desc',null);
		$MasterCourse = M('MasterCourse');
		$MasterCourse->create($m);
		$cid = $MasterCourse->add();
		
		/*
		$config = array(    
                    //'maxSize'    =>    3145728,    
                    'rootPath'   =>    './Public/',
                    'savePath'   =>    'Upload/',    
                    'saveName'   =>    array('uniqid',''),    
                    // 'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),    
                    'autoSub'    =>    true,    
                    'subName'    =>    array('date','Ymd','time'),
                 );
        $upload = new \Think\Upload($config);// 实例化上传类
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }    

        $info   =   $upload->upload();  
        
        if(!$info) {// 上传错误提示错误信息        
            //$this->error('上传课件失败！',U('Master/course_list'));
            $this->error($upload->getError());
        }else{// 上传成功     			
			$MasterCourseware = M('MasterCourseware');
			foreach ($info as $file) {
				$tmp = mydto();
				$tmp['course_id'] = $cid;	
				$tmp['path'] = $file['rootpath'].$file['savepath'].$file['savename'];
				$tmp['name'] = $file['name'];
				$tmp['url'] = '/Public/'.str_replace("\\", "/", $tmp['path']);
				$MasterCourseware->create($tmp);
				$MasterCourseware->add();
			}
        }
        */
        $names = I('crw_name',null);
        $urls = I('crw_url',null);
        $crwok = true;
        if(is_null($names) || is_null($urls)){
        	$crwok = false;
        }elseif (count($names) != count($urls)) {
        	$crwok = false;
        }

        if($crwok){
        	$MasterCourseware = M('MasterCourseware');
        	for ($i=0; $i <count($names) ; $i++) { 
        		$name = $names[$i];
        		$url = $urls[$i];
        		$tmp = mydto();
        		$tmp['course_id'] = $cid;
        		$tmp['name'] = $name;
        		$tmp['url'] = $url;
        		$MasterCourseware->create($tmp);
        		$MasterCourseware->add();
        	}
        }

		$this->success('成功添加课程！',U('Master/course_list'));				
	}
		
	
	public function course_edit(){
	    $id = I("id",null);
        if(!$id || is_null($id)){
            $this->error("没有提供ID！");
        }
        
        $C = M("MasterCourse");
        $c = $C->where(array('active' => 0 , 'id' => intval($id)))->find();
        if(!$c || is_null($c)){
            $this->error("该记录不存在！");
        }
        
        $CRW = M("MasterCourseware");
        $crws = $CRW->where(array('active' => 0 , 'course_id' => $c['id']))->order('id')->select();
        $this->c = $c;
        $this->crws = $crws;
        $this->display();
	}
	
	public function course_edit_handle(){
        $id = I("id",null);
        if(!$id || is_null($id)){
            $this->error("没有提供ID！");
        }
        $C = M("MasterCourse");
        $c = $C->where(array('active' => 0 , 'id' => intval($id)))->find();
        if(!$c || is_null($c)){
            $this->error("该记录不存在！");
        }
        
        $C->name = I('name',null);
        $C->desc = I('desc',null);
        $C->update_by_id = session('user_id');
        $C->update_time = mynow();
        $C->where(array('id' => $c['id']))->save(); 
        
        //del the files
        $dels = I("delcrw",array());
        $CRW = M("MasterCourseware");
        $CRW->active = 1;
        $CRW->update_by_id = session('user_id');
        $CRW->update_time = mynow();
        $CRW->where(array('id' => array('in',$dels)))->save();
        
        /*
        //save the upload new file
        $config = array(    
                    //'maxSize'    =>    3145728,    
                    'rootPath'   =>    './Public/',
                    'savePath'   =>    'Upload/',    
                    'saveName'   =>    array('uniqid',''),    
                    // 'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),    
                    'autoSub'    =>    true,    
                    'subName'    =>    array('date','Ymd','time'),
                 );
        $upload = new \Think\Upload($config);// 实例化上传类
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }    
        $info   =   $upload->upload();  
        if($info) {
            // 上传成功               
            foreach ($info as $file) {
                $tmp = mydto();
                $tmp['course_id'] = $c['id'];   
                $tmp['path'] = $file['rootpath'].$file['savepath'].$file['savename'];
                $tmp['name'] = $file['name'];
                $tmp['url'] = '/Public/'.str_replace("\\", "/", $tmp['path']);
                $CRW->create($tmp);
                $CRW->add();
            }
        }
        */
        $names = I('crw_name',null);
        $urls = I('crw_url',null);
        $crwok = true;
        if(is_null($names) || is_null($urls)){
        	$crwok = false;
        }elseif (count($names) != count($urls)) {
        	$crwok = false;
        }

        if($crwok){
        	$MasterCourseware = M('MasterCourseware');
        	for ($i=0; $i <count($names) ; $i++) { 
        		$name = $names[$i];
        		$url = $urls[$i];
        		$tmp = mydto();
        		$tmp['course_id'] = $c['id'];
        		$tmp['name'] = $name;
        		$tmp['url'] = $url;
        		$MasterCourseware->create($tmp);
        		$MasterCourseware->add();
        	}
        }
        $this->success('成功修改课程！',U('Master/course_list'));
	}
        
	
	public function course_list_delete_handle(){
		$checkbox_array=I('id',array());
		$master_course = M("master_course"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		if($master_course->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('master/course_list'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的课程...');	
		}
	}
	
	
	public function courseware_add()
	{
		$this->display();
	}

	public function courseware_add_handle()
	{
		$names = I("name",array());
        $types = I("type",array());
        $urls = I("url",array());
        for ($i=0; $i < count($names); $i++) {
            $name = $names[$i];
            $type = $types[$i];
            $url = $urls[$i];
            if(!$name || is_null($name)){
                continue;
            }
            
            $M = M("MasterCourseware");
            if($type == 'G'){
                $M = M("MasterGame");    
            }
            
            $dto = mydto();
            $dto['name'] = $name;
            $dto['url'] = $url;
            $M->create($dto);
            $M->add();  
        }
        $this->success("成功添加课件与游戏。");
	}
	
	
    public function courseware_list(){
        if(!isset($_GET['p']))
            {
                $_GET['p'] = 1;
            }
            
        $master_class = M('MasterCourseware')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
        // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $this->assign('master_class',$master_class);// 赋值数据集
        $count = M('master_class')->where('active=0')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    
}