<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function _initialize(){
        if(!session('?login')){
            $this->redirect('Auth/login',NULL,0);
        }
		
		//更新在线时间
		//$system_current_user = M("system_current_user"); // 实例化User对象
		$map['user_key']  = session('user_key');
		$data['time_stamp'] = mynow();
		//$system_current_user=M("system_current_user")->where($map)->setField($data);
		$system_current_user = M("system_current_user")->where($map)->find($data);
		if($system_current_user)
		{
			$system_current_user = M("system_current_user")->where($map)->setField($data);
		}
		else
		{
			session('login',null); 
			$this->error('登录已超时，请重新登录!',U('Auth/login'),3);
			//$this->redirect('Auth/login',NULL,0);
		}

    }
	
}
        