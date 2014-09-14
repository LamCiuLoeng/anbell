<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class TestController extends BaseController {
    public function index(){
		$this->user_id=session('user_id');
		$this->user_key=session('user_key');
        $this->display();
	}
}