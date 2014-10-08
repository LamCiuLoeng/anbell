<?php
namespace Home\Controller;
use Think\Controller;
class SafesubjectController extends Controller {
    public function index(){
		//$web_news=M('web_news')->where('active=0')->select();
		//$this->assign('web_news',$web_news);
		$map=array();
		$map['active']=0;
		$web_safe_subject = M("web_safe_subject")->where($map)->select();
		//$this->assign('web_safe_subject',$web_safe_subject);
		
		$all_data=$web_safe_subject;
		
		
		foreach($all_data as $key=>$val){
			$m['active']=0;
			$m['safe_subject_id']=$all_data[$key]['id'];
			$all_data[$key]['data_info']=M("web_safe_resource")->where($m)->limit(6)->select();
		}
		$this->assign('all_data',$all_data);
		$this->display();
    }
	
	public function safe_subject_page(){
		
		$map=array();
		$map['id'] = I('id');
		$map['active'] = '0';
		
		$web_safe_subject_one=M('web_safe_subject')->where($map)->find();
		$this->assign('web_safe_subject_one',$web_safe_subject_one);
			
		$map=array();
		$map['safe_subject_id']=$web_safe_subject_one['id'];
		$map['active']=0;
		$all_data=M("web_safe_resource")->where($map)->select();
		$this->assign('all_data',$all_data);
		
		//p($all_data);
		//die;
		$this->display();
    }
	
	public function safesubject_page(){
		$map=array();
		$map['active']=0;
		$web_knowledge_category = M("web_knowledge_category")->where($map)->select();
		$this->assign('web_knowledge_category',$web_knowledge_category);
		
		$map=array();
		$map['id'] = I('id');
		$web_safe_resource=M('web_safe_resource')->where($map)->find();
		//p($web_safe_resource);
		//die;
		$this->assign('web_safe_resource',$web_safe_resource);
		$this->display();
    }

}