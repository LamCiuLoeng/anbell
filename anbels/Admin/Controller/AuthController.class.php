<?php
namespace Admin\Controller;
use Think\Controller;
class AuthController extends Controller {
    
    public function login(){
        $this->display();
	}
    
    public function check()
    {
		$system_no = I('system_no',NULL);
		$pw = I('password',NULL);
		$result = login_check($system_no,$pw);
		if($result['flag'] != 0){
			$this->error($result['msg'],U('login'));
		}else{
			session('login','YES');
			session('user_id',$result['user']['id']);
			$key = generatekey();
			$CurrentUser =  M('SystemCurrentUser');			
			$CurrentUser->data(array('user_key' => $key,'time_stamp' => mynow() ))->add();
			session('user_key' , $key);
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