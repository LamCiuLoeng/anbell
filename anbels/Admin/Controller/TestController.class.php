<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class TestController extends BaseController {
    public function index(){
		$info['用户ID']=session('user_id');
		$info['钥匙']=session('user_key');
		$info['地区']='北京';
		$info['性别']='男';
		$info['类型']='管理员';
		//p($info);
        //$this->display();
		foreach($info as $key=> $val){
			p($key);
			p($val);
		}
	}
}