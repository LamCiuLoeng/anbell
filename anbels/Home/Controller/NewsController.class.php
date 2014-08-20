<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function index(){
		$web_news=M('web_news')->where('active=0')->select();
		$this->assign('web_news',$web_news);
		$this->display('news');
    }
	
	public function anbels_info(){
		$map['category'] = '安贝尔动态';
		$map['active'] = '0';
		$web_news=M('web_news')->where($map)->select();
		$this->assign('web_news',$web_news);
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
	
	public function news_page(){
		$map['id'] = I('id');
		$web_news=M('web_news')->where($map)->find();
		$this->assign('web_news',$web_news);
		$this->display();
    }

}