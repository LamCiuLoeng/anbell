<?php
namespace Api\Controller;
use Think\Controller;
class AuthController extends Controller {
    public function login()
    {
        $this->ajaxReturn(array('aa' => 'bb' , 'cc' => 'dd'));
    }
    
}
    