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
		//$this->assign('master_location',M('master_location')->select());
		$master_school=M('master_school')->where('active=0')->select();
		$this->assign('master_school',$master_school);
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
		$data['active'] = 0;
		$data['create_time'] = mynow();
		$data['update_time'] = mynow();
		//p($master_school->add($data));die;
		if($master_school->add($data)){
			$this->success('添加成功！',U('master/school_list'));
		} else {
			$this->error('发布失败，请重试...');	
		}
	}
	
	public function school_edit(){
		if(I('post.checkbox')){
			$checkbox_array=I('post.checkbox');
			$map['id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$master_school=M('master_school')->where($map)->select();
			$this->assign('master_school',$master_school);
			$this->display();
			} else {
			$this->show('请选择所要修改的学校，重试...');	
		}
	}
	
	public function school_edit_handle(){
		$master_school = M("master_school"); // 实例化User对象
		$map['id'] = I('post.id');
		$data['location_id'] = I('post.location');
		$data['name'] = I('post.school_name');
		$data['desc'] = I('post.description');
		$data['update_time'] = mynow();
		if($master_school->where($map)->setField($data)){
			$this->success('修改成功！',U('master/school_list'));
		} else {
			$this->error('修改失败，请重试...');	
		}
	}
	
	public function school_list_delete_handle(){
		$checkbox_array=I('post.checkbox');
		//$aaaa=$_POST['checkbox'];
		//p($dddd);
		//die;
		$master_school = M("master_school"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		$data['active'] = 1;
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($master_school->where($map)->setField($data)){
			$this->success('删除成功！',U('master/school_list'));
		} else {
			$this->error('删除失败，请勾选需要删除的学校...');	
		}
	}
	
	
	public function ssq_handle(){
		$master_location = M("master_location"); // 实例化User对象
		$data = $master_location->select();
		$this->ajaxReturn($data);
	}
	
	
	
	
	
}