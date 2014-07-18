<?php
namespace Admin\Controller;
use Think\Controller;
class AuthController extends Controller {
    
    public function login(){
        $this->display();
	}
    
    public function check()
    {
		$name = I('name',NULL);
		$pw = I('password',NULL);
		$result = login_check($name,$pw);
		if($result['flag'] != 0){
			$this->error($result['msg'],U('login'));
		}else{
			session('login','YES');
            $this->redirect('Index/index',NULL,0);
		}
		
    }
    
    
    public function logout()
    {
        if(session('?login')){
            session('login',null); 
        }
        $this->success('',U('login'),2);
    }
    
	
	public function test()
	{
		echo crypt("100","dingnigefei").'<br />';
		echo crypt("101","dingnigefei").'<br />';
		echo crypt("102","dingnigefei").'<br />';
	}
	
}