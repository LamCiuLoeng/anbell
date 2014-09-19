<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;


class WebsiteController extends BaseController {
    public function index(){
        $this->display();
	}
	
	public function aboutanbels()
	{
		$map['page'] = 'aboutanbels';
		$web_content=M('web_content')->where($map)->find();
		$this->assign('web_content',$web_content);
		$this->display();
	}
	
	public function aboutanbels_edit_handle()
	{
		$web_content = M("web_content"); // 实例化User对象
		$map['page'] = 'aboutanbels';
		$data['remarks'] = I('remarks');
		$data['content'] = I('content1');
		
		if($web_content->where($map)->setField($data))
		{
			$this->success('关于安贝尔修改成功！',U('aboutanbels'));
		} 
		else 
		{
			$this->error('关于安贝尔修改失败，请重试...');	
		}

	}
	
	public function aboutus()
	{
		$map['page'] = 'aboutus';
		$web_content=M('web_content')->where($map)->find();
		$this->assign('web_content',$web_content);
		$this->display();
	}
	
	public function aboutus_edit_handle()
	{
		$web_content = M("web_content"); // 实例化User对象
		$map['page'] = 'aboutus';
		$data['remarks'] = I('remarks');
		$data['content'] = I('content1');
		
		if($web_content->where($map)->setField($data))
		{
			$this->success('关于普安世嘉修改成功！',U('website/aboutus'));
		} 
		else 
		{
			$this->error('关于普安世嘉修改失败，请重试...');	
		}

	}
	
	public function contactus()
	{
		$map['page'] = 'contactus';
		$web_content=M('web_content')->where($map)->find();
		$this->assign('web_content',$web_content);
		$this->display();
	}
	
	public function contactus_edit_handle()
	{
		$web_content = M("web_content"); // 实例化User对象
		$map['page'] = 'contactus';
		$data['remarks'] = I('remarks');
		$data['content'] = I('content1');
		
		if($web_content->where($map)->setField($data))
		{
			$this->success('联系我们修改成功！',U('website/contactus'));
		} 
		else 
		{
			$this->error('联系我们修改失败，请重试...');	
		}

	}
	
	
	public function knowledge_category_list()
	{
		$map['active']=0;
		$web_knowledge_category=M('web_knowledge_category')->where($map)->select();
		$this->assign('web_knowledge_category',$web_knowledge_category);
		//p($web_knowledge_category);
		//die;
		$this->display();
	}
	
	public function knowledge_category_add()
	{
		$this->display();
	}
	
	public function knowledge_category_add_handle()
	{
		$data=mydto();
		$config = array(    
				   /* 'rootPath'   =>    './Public/',
					'savePath'   =>    'Upload/pic/',    
					'saveName'   =>    array('date','YmdHis'),    
					'exts'       =>    array('jpg', 'jpge', 'png', 'gif'),   
					'autoSub'    =>    true,    
					'subName'    =>    array('date','Ymd','time'),*/
					
					'maxSize'    =>    5242880,
					'rootPath'   =>    './Public/',
					'savePath'   =>    'Upload/pic/',
					'saveName'   =>    array('uniqid','pic'),
					'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Ymd'),
				 );
		$upload = new \Think\Upload($config);// 实例化上传类
		if(!file_exists($upload->savePath)){
			mkdir($upload->savePath);
		}    
		$info   =   $upload->uploadOne($_FILES["pic"]); 
		if($info) {
		
			// 上传成功             
			$path =  '/public/'.$info['savepath'].$info['savename']; 
			$data['img_path'] = $path;                        
		}else{
			if($upload->getError() != '没有文件被上传！'){
				$this->error($upload->getError()); 
			}
		}
		
		
		$web_knowledge_category = M("web_knowledge_category"); // 实例化User对象
		$data['name'] = I('name');
		$data['desc'] = I('desc');
		if($web_knowledge_category->add($data))
		{
			$this->success('知识分类添加成功！',U('knowledge_category_list'));
		} 
		else 
		{
			$this->error('知识分类添加失败，请重试...');	
		}

	}
	
	public function knowledge_category_edit()
	{
		$id = I('id',null);
        if(!$id || is_null($id)){
            $this->error("没有提供ID！");
        }
        $map['active']=0;
		$map['id']=intval($id);
        $web_knowledge_category=M('web_knowledge_category')->where($map)->find();
        if(!$web_knowledge_category || is_null($web_knowledge_category)){
            $this->error("该记录不存在！");
        }
		$this->assign('web_knowledge_category',$web_knowledge_category);
		$this->display();
	}
	
	public function knowledge_category_edit_handle()
	{
		$data=mydto_edit();
		$config = array(    
				   /* 'rootPath'   =>    './Public/',
					'savePath'   =>    'Upload/pic/',    
					'saveName'   =>    array('date','YmdHis'),    
					'exts'       =>    array('jpg', 'jpge', 'png', 'gif'),   
					'autoSub'    =>    true,    
					'subName'    =>    array('date','Ymd','time'),*/
					
					'maxSize'    =>    5242880,
					'rootPath'   =>    './Public/',
					'savePath'   =>    'Upload/pic/',
					'saveName'   =>    array('uniqid','pic'),
					'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Ymd'),
				 );
				 
		$upload = new \Think\Upload($config);// 实例化上传类
		if(!file_exists($upload->savePath)){
			mkdir($upload->savePath);
		}    
		$info   =   $upload->uploadOne($_FILES["pic"]);  
		if($info) {
		
			// 上传成功             
			$path =  '/public/'.$info['savepath'].$info['savename'];   
			$data['img_path'] = $path;  
			
			                
		}else{
			if($upload->getError() != '没有文件被上传！'){
				$this->error($upload->getError()); 
			}
		}
		
		
		$web_knowledge_category = M("web_knowledge_category"); // 实例化User对象
		$map['id'] = I('post.id');
		$data['name'] = I('name');
		$data['desc'] = I('desc');
		if($web_knowledge_category->where($map)->setField($data))
		{
			$this->success('知识分类修改成功！',U('knowledge_category_list'));
		} 
		else 
		{
			$this->error('知识分修改失败，请重试...');	
		}

	}
	
	public function knowledge_category_delete_handle()
    {
		$ids = I("id",null);
		$checkbox_array=$ids;
		$web_knowledge_category = M("web_knowledge_category"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		//p($map);
		//die;
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($web_knowledge_category->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('knowledge_category_list'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的账号...');	
		}
    }
	
	public function safe_resource_list()
	{
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
		$map['anbels_web_safe_resource.active']=0;
		$web_safe_resource=M('web_safe_resource')
		->join('left join anbels_web_knowledge_category  on  anbels_web_safe_resource.knowledge_category_id = anbels_web_knowledge_category.id')
		->field('anbels_web_safe_resource.title,anbels_web_safe_resource.date_time,anbels_web_safe_resource.grade_category,
				anbels_web_safe_resource.img_path as img_path,anbels_web_safe_resource.id as id,
				anbels_web_knowledge_category.name as knowledge_category')
		->where($map)
		->page($_GET['p'].',4')
		->select();
		$this->assign('web_safe_resource',$web_safe_resource);// 赋值数据集
		
		$count = M('web_safe_resource')
		->where($map)
		->count();// 查询满足要求的总记录数
		
		$Page = new \Think\Page($count,4);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		//p($web_safe_resource);
		//die;
		$this->display();
	}
	
	public function safe_resource_add()
	{
		$map['active']=0;
		$web_knowledge_category = M("web_knowledge_category")->where($map)->select();
		$this->assign('web_knowledge_category',$web_knowledge_category);
		$this->display();
	}
	
	public function safe_resource_add_handle()
	{
		$data=mydto();
		$config = array(    
				   /* 'rootPath'   =>    './Public/',
					'savePath'   =>    'Upload/pic/',    
					'saveName'   =>    array('date','YmdHis'),    
					'exts'       =>    array('jpg', 'jpge', 'png', 'gif'),   
					'autoSub'    =>    true,    
					'subName'    =>    array('date','Ymd','time'),*/
					
					'maxSize'    =>    5242880,
					'rootPath'   =>    './Public/',
					'savePath'   =>    'Upload/pic/',
					'saveName'   =>    array('uniqid','pic'),
					'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Ymd'),
				 );
		$upload = new \Think\Upload($config);// 实例化上传类
		if(!file_exists($upload->savePath)){
			mkdir($upload->savePath);
		}    
		$info   =   $upload->uploadOne($_FILES["pic"]);  
		if($info) {
		
			// 上传成功             
			$path =  '/public/'.$info['savepath'].$info['savename'];  
			$data['img_path'] = $path;                      
		}else{
			if($upload->getError() != '没有文件被上传！'){
				$this->error($upload->getError()); 
			}
		}
		
		
		$web_safe_resource = M("web_safe_resource"); // 实例化User对象
		$data['knowledge_category_id'] = I('knowledge_category');
		$data['grade_category'] = I('grade_category');
		$data['title'] = I('resource_title');
		$data['date_time'] = I('date_time');
		$data['content'] = I('content1');
		if($web_safe_resource->add($data))
		{
			$this->success('安全资源添加成功！',U('safe_resource_list'));
		} 
		else 
		{
			$this->error('安全资源类添加失败，请重试...');	
		}

	}
	
	public function safe_resource_delete_handle()
    {
		$ids = I("id",null);
		$checkbox_array=$ids;
		$web_safe_resource = M("web_safe_resource"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		//p($map);
		//die;
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($web_safe_resource->where($map)->setField($data))
		{
			$this->success(count($checkbox_array).'条记录删除成功！',U('safe_resource_list',I('get.')));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的账号...');	
		}
    }
	
}