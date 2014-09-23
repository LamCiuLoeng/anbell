<?php
namespace Home\Controller;
use Think\Controller;
class SaferesourceController extends Controller {
    public function index(){
		//$web_news=M('web_news')->where('active=0')->select();
		//$this->assign('web_news',$web_news);
		
		$map['active']=0;
		$web_knowledge_category = M("web_knowledge_category")->where($map)->select();
		$this->assign('web_knowledge_category',$web_knowledge_category);
		
		$web_safe_resource = M("web_safe_resource")->where($map)->limit(3)->select();
		$this->assign('web_safe_resource',$web_safe_resource);
		
		$all_data=$web_knowledge_category;
		
		
		foreach($all_data as $key=>$val){
			$m['active']=0;
			$m['knowledge_category_id']=$all_data[$key]['id'];
			$all_data[$key]['data_info']=M("web_safe_resource")->where($m)->limit(6)->select();
		}
		//p($all_data);
		//die;
		$this->assign('all_data',$all_data);
		$this->display();
    }
	
	public function knowledge_category_page(){
		$map=array();
		$map['active']=0;
		$web_knowledge_category = M("web_knowledge_category")->where($map)->select();
		$this->assign('web_knowledge_category',$web_knowledge_category);
		
		$map=array();
		$map['id'] = I('id');
		$map['active'] = '0';
		
		$web_knowledge_category_one=M('web_knowledge_category')->where($map)->find();
		$this->assign('web_knowledge_category_one',$web_knowledge_category_one);
			
		$map=array();
		$map['knowledge_category_id']=$web_knowledge_category_one['id'];
		$map['active']=0;
		$all_data=M("web_safe_resource")->where($map)->select();
		$this->assign('all_data',$all_data);
		
		$web_safe_resource = M("web_safe_resource")->where($map)->limit(3)->select();
		$this->assign('web_safe_resource',$web_safe_resource);
		
		//p($all_data);
		//die;
		$this->display();
    }
	
	public function grade_category_page(){
		$map=array();
		$map['active']=0;
		$web_knowledge_category = M("web_knowledge_category")->where($map)->select();
		$this->assign('web_knowledge_category',$web_knowledge_category);
		
		$map=array();
		$map['grade_category'] = I('grade');
		$map['active'] = '0';
		
		$grade_category=$map['grade_category'];
		$this->assign('grade_category',$grade_category);
			
		$all_data=M("web_safe_resource")->where($map)->select();
		$this->assign('all_data',$all_data);
		
		$web_safe_resource = M("web_safe_resource")->where($map)->limit(3)->select();
		$this->assign('web_safe_resource',$web_safe_resource);
		
		//p($all_data);
		//die;
		$this->display();
    }
	
	public function news_info(){
		$map['category'] = '新闻动态';
		$map['active'] = '0';
		$web_news=M('web_news')->where($map)->select();
		$this->assign('web_news',$web_news);
		$this->display();
    }
	
	public function policy_info(){
		$map['category'] = '政策动态';
		$map['active'] = '0';
		$web_news=M('web_news')->where($map)->select();
		$this->assign('web_news',$web_news);
		$this->display();
    }
	
	public function saferesource_page(){
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