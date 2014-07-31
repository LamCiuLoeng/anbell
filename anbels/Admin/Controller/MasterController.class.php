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
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
			
		$master_school = M('master_school')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$this->assign('master_school',$master_school);// 赋值数据集
		$count = M('master_school')->where('active=0')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		die();
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
		$data['create_by_id'] = session('user_id');
		$data['create_time'] = mynow();
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->add($data));die;
		if($master_school->add($data))
		{
			$this->success('添加成功！',U('master/school_list'));
		} 
		else 
		{
			$this->error('发布失败，请重试...');	
		}
	}
	
	public function school_edit(){
		if(I('post.checkbox'))
		{
			$checkbox_array=I('post.checkbox');
			$map['id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$master_school=M('master_school')->where($map)->select();
			$this->assign('master_school',$master_school);
			$this->display();
		} 
		else 
		{
			$this->error('请选择所要修改的学校，重试...','',2);	
		}
	}
	
	public function school_edit_handle(){
		$master_school = M("master_school"); // 实例化User对象
		$map['id'] = I('post.id');
		$data['location_id'] = I('post.location');
		$data['name'] = I('post.school_name');
		$data['desc'] = I('post.description');
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		if($master_school->where($map)->setField($data))
		{
			$this->success('修改成功！',U('master/school_list'));
		} 
		else 
		{
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
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($master_school->where($map)->setField($data))
		{
			$this->success('删除成功！',U('master/school_list'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的学校...');	
		}
	}
	
	public function class_list(){
		if(!isset($_GET['p']))
			{
				$_GET['p'] = 1;
			}
			
		$master_class = M('master_class')->where('active=0')->order('create_time')->page($_GET['p'].',7')->select(); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$this->assign('master_class',$master_class);// 赋值数据集
		$count = M('master_class')->where('active=0')->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count,7);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		die();
		$master_class=M('master_class')->where('active=0')->select();
		$this->assign('master_class',$master_class);
		$this->display();
	}
	public function class_add(){
        $this->display();
	}
	public function class_add_handle(){
		$master_class = M("master_class"); // 实例化User对象
		$data['school_id'] = I('post.school_name');
		$data['name'] = I('post.class_name');
		$data['grade'] = I('post.grade');
		$data['desc'] = I('post.description');
		$data['active'] = 0;
		$data['create_by_id'] = session('user_id');
		$data['create_time'] = mynow();
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->add($data));die;
		if($master_class->add($data))
		{
			$this->success('添加成功！',U('master/class_list'));
		} 
		else 
		{
			$this->error('发布失败，请重试...');	
		}
	}
	
	public function class_edit(){
		if(I('post.checkbox'))
		{
			$checkbox_array=I('post.checkbox');
			$map['id'] = $checkbox_array[0];
			//p($checkbox_array[0]);die;
			$master_class=M('master_class')->where($map)->select();
			$this->assign('master_class',$master_class);
			$this->display();
		} 
		else 
		{
			$this->error('请选择所要修改的学校，重试...','',2);	
		}
	}
	
	public function class_edit_handle(){
		$master_class = M("master_class"); // 实例化User对象
		$map['id'] = I('post.id');
		$data['school_id'] = I('post.school_name');
		$data['name'] = I('post.class_name');
		$data['grade'] = I('post.grade');
		$data['desc'] = I('post.description');
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		if($master_school->where($map)->setField($data))
		{
			$this->success('修改成功！',U('master/school_list'));
		} 
		else 
		{
			$this->error('修改失败，请重试...');	
		}
	}
	
	public function class_list_delete_handle(){
		$checkbox_array=I('post.checkbox');
		//$aaaa=$_POST['checkbox'];
		//p($dddd);
		//die;
		$master_class = M("master_class"); // 实例化User对象
		$map['id']  = array('in',$checkbox_array);
		$data['active'] = 1;
		$data['update_by_id'] = session('user_id');
		$data['update_time'] = mynow();
		//p($master_school->where($map)->setField('active',1));
		if($master_class->where($map)->setField($data))
		{
			$this->success('删除成功！',U('master/class_list'));
		} 
		else
		{
			$this->error('删除失败，请勾选需要删除的学校...');	
		}
	}
	
	
	
	
	public function ssq_handle(){
		$master_location = M("master_location"); // 实例化User对象
		$data = $master_location->select();
		$this->ajaxReturn($data);
	}
	
	public function request_school(){
		$master_school = M("master_school"); // 实例化User对象
		$data = $master_school->where('active=0')->select();
		$this->ajaxReturn($data);
	}
	
	
	
}