<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;


class IndexController extends BaseController {
    public function index(){
		//echo 'hello';
		//p($_GET);die;
		//echo U('index/master@blog.thinkphp.cn',array('uid'=>1));die;
		$user_id=session('user_id');
		$auth_user = M("auth_user"); // 实例化User对象
		$user = $auth_user->where('id='.$user_id)->find();
		$this->assign('user',$user);
        $this->display();
	}
	
	public function test()
	{
		echo authcheck('aabb',1,'or','yes','no');
	}
}