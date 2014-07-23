<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class IndexController extends BaseController {
    public function index(){
		//echo 'hello';
		//p($_GET);die;
		//echo U('index/master@blog.thinkphp.cn',array('uid'=>1));die;
        $this->display();
	}
	
	public function account_manage(){
		//$this->assign('a',1111);
		//$this->a=222;
		
		// 使用M方法实例化
		//$auth_user = M('auth_user')->select();
		// 和用法 $User = new \Think\Model('User'); 等效
		// 执行其他的数据操作
		//$auth_user->select();
		$this->assign('auth_user',M('auth_user')->select())->display();
		
		//$this->auth_user = M('auth_user')->select();
		//echo p($auth_user);
		//echo $auth_user[0][name];
		//p($auth_user);
		//die;
        //$this->display();
	}
	
	public function master(){
        $this->display();
	}
	
	
}