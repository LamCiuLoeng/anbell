<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$this->display();
    }
    
    public function showpw(){
        $pw = I('pw');
		$this->ec($pw);
		
    }
	
	public function ec($pw='',$key=null)
	{
		$c1 =  authcode($string=$pw,$key=$key);
		echo $c1.'<br />';
		$p1 = authcode($string=$c1,$operation = 'DECODE',$key=$key);
		echo $p1.'<br />';
	}
	
}