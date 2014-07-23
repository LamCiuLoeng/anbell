<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class AccountManagementController extends BaseController {
    public function index(){
		//echo 'hello';
		//p($_GET);die;
		//echo U('index/master@blog.thinkphp.cn',array('uid'=>1));die;
        $this->assign('auth_user',M('auth_user')->select())->display();
	}
	
	
}