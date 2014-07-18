<?php
namespace Api\Controller;
use Think\Controller;
class AuthController extends Controller {
    public function login()
    {		
		$name = I('name',NULL);
		$password = I('password',NULL);
		$result = login_check($name,$password);
		if($result[flag] == 0 ){
			$result['key'] = generatekey();
			$result['msg'] = '登陆成功';
		}
		$this->ajaxReturn($result);		
    }
}
    