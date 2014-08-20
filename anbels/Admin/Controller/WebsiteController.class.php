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
	
}