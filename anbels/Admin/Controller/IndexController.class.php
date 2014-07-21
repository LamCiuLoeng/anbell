<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class IndexController extends BaseController {
    public function index(){
        $this->display();
	}
	public function master(){
        $this->display();
	}
	public function account_manage(){
        $this->display();
	}
}