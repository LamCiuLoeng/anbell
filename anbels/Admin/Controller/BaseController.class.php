<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function _initialize(){
        if(!session('?login')){
            $this->redirect('Auth/login',NULL,0);
        }
    }
}
        