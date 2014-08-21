<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$this->display();
    }
    
    public function showpw(){
        $pw = I('pw');
        echo crypt($pw,"dingnigefei");
    }
}