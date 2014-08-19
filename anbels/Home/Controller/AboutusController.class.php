<?php
namespace Home\Controller;
use Think\Controller;
class AboutusController extends Controller {
    public function index(){
		$map['page'] = 'aboutus';
		$web_content=M('web_content')->where($map)->find();
		$this->assign('web_content',$web_content);
		$this->display('aboutus');
    }
	
	public function contactus(){
		$map['page'] = 'contactus';
		$web_content=M('web_content')->where($map)->find();
		$this->assign('web_content',$web_content);
		$this->display();
    }

	
}