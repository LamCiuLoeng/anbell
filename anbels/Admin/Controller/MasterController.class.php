<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class MasterController extends BaseController {
    public function index(){
		//echo 'hello';
		//p($_GET);die;
		//echo U('index/master@blog.thinkphp.cn',array('uid'=>1));die;
        $this->display();
	}
	
	public function school_list(){
		//echo qu('4904');die;
		$this->assign('master_school',M('master_school')->select());
        $this->assign('master_location',M('master_location')->select());
		$this->display();
	}
	
	public function school_add(){
        $this->display();
	}
	public function school_add_handle(){
		$master_school = M("master_school"); // 实例化User对象
		$data['location_id'] = I('post.location');
		$data['name'] = I('post.school_name');
		$data['desc'] = I('post.description');
		$data['create_time'] = mynow();
		$data['update_time'] = mynow();
		//p($master_school->add($data));die;
		if($master_school->add($data)){
			$this->success('添加成功！',U('master/school_list'));
		} else {
			$this->error('发布失败，请重试...');	
		}
	}
	
	
	public function ssq_handle(){
		$master_location = M("master_location"); // 实例化User对象
		$data = $master_location->select();
		$this->ajaxReturn($data);
	}
	
	
	
	
	
}